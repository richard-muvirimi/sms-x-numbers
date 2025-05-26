<?php

namespace Tests\Feature\Commands;

use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanupExpiredFilesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_cleanup_command_removes_expired_files()
    {
        Storage::fake('local');
        // Create expired upload
        $expiredUpload = Upload::factory()->create([
            'created_at' => now()->subDays(31),
        ]);
        $expiredUpload->original_path = 'uploads/original/'.$expiredUpload->id.'.txt';
        $expiredUpload->save();

        $recentUpload = Upload::factory()->create([
            'created_at' => now()->subDays(29),
        ]);
        $recentUpload->original_path = 'uploads/original/'.$recentUpload->id.'.txt';
        $recentUpload->save();

        Storage::put($expiredUpload->original_path, 'test content');
        Storage::put($recentUpload->original_path, 'test content');

        // Run command
        $this->artisan('files:cleanup')
            ->expectsOutput('Starting cleanup of expired files...')
            ->expectsOutput('Cleanup completed successfully.')
            ->assertSuccessful();

        // Assert expired upload was removed
        $this->assertDatabaseMissing('uploads', ['id' => $expiredUpload->id]);
        $this->assertFalse(Storage::exists($expiredUpload->original_path));

        // Assert recent upload remains
        $this->assertDatabaseHas('uploads', ['id' => $recentUpload->id]);
        $this->assertTrue(Storage::exists($recentUpload->original_path));
    }

    public function test_cleanup_command_handles_errors()
    {
        // Mock FileUploadService to throw an exception
        $this->mock(\App\Services\FileUploadService::class)
            ->shouldReceive('deleteExpiredFiles')
            ->once()
            ->andThrow(new \Exception('Test error'));

        $this->artisan('files:cleanup')
            ->expectsOutput('Starting cleanup of expired files...')
            ->expectsOutput('Error during cleanup: Test error')
            ->assertFailed();
    }
}
