<?php

namespace Tests\Unit\Services;

use App\Models\Upload;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadServiceTest extends TestCase
{
    use RefreshDatabase;

    private FileUploadService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FileUploadService::class);
    }

    public function test_handle_upload_creates_upload_record_from_file()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('numbers.csv');
        $service = new FileUploadService;

        $upload = $service->handleUpload($file, null, 'US', 1000);

        $this->assertInstanceOf(Upload::class, $upload);
        $this->assertDatabaseHas('uploads', [
            'id' => $upload->id,
            'name' => 'numbers.csv',
        ]);
        $this->assertTrue(Storage::disk('local')->exists($upload->original_path));
    }

    public function test_handle_upload_creates_upload_record_from_text()
    {
        Storage::fake('local');
        $service = new FileUploadService;

        $upload = $service->handleUpload(null, "1234567890\n2345678901", 'US', 1000);

        $this->assertInstanceOf(Upload::class, $upload);
        $this->assertDatabaseHas('uploads', [
            'id' => $upload->id,
        ]);
        $this->assertTrue(Storage::disk('local')->exists($upload->original_path));
    }

    public function test_delete_expired_files_removes_old_records()
    {
        Storage::fake('local');
        $expiredUpload = Upload::factory()->create([
            'created_at' => now()->subDays(31),
        ]);
        $expiredUpload->original_path = 'uploads/original/'.$expiredUpload->id.'.txt';
        $expiredUpload->save();
        Storage::disk('local')->put($expiredUpload->original_path, 'test content');

        $recentUpload = Upload::factory()->create([
            'created_at' => now()->subDays(29),
        ]);
        $recentUpload->original_path = 'uploads/original/'.$recentUpload->id.'.txt';
        $recentUpload->save();
        Storage::disk('local')->put($recentUpload->original_path, 'test content');

        $this->service->deleteExpiredFiles();

        $this->assertDatabaseMissing('uploads', ['id' => $expiredUpload->id]);
        $this->assertDatabaseHas('uploads', ['id' => $recentUpload->id]);

        $this->assertFalse(Storage::disk('local')->exists($expiredUpload->original_path));
        $this->assertTrue(Storage::disk('local')->exists($recentUpload->original_path));
    }
}
