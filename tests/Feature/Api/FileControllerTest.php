<?php

namespace Tests\Feature\Api;

use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_process_file_upload()
    {
        $file = UploadedFile::fake()->createWithContent(
            'numbers.csv',
            "1234567890\n0987654321"
        );

        $response = $this->postJson('/api/v1/files/process', [
            'file' => $file,
            'country_code' => 'US',
            'chunk_size' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'original_file' => [
                        'name',
                        'download_url',
                    ],
                    'chunks',
                    'stats' => [
                        'total',
                        'valid',
                        'invalid',
                    ],
                    'view_url',
                    'created_at',
                    'expires_at',
                ],
            ]);

        $uploadId = $response->json('data.id');
        $upload = Upload::find($uploadId);

        $this->assertDatabaseHas('uploads', [
            'id' => $uploadId,
            'original_path' => $upload->original_path,
        ]);

        $this->assertTrue(Storage::disk('local')->exists($upload->original_path));
    }

    public function test_process_text_content()
    {
        $response = $this->postJson('/api/v1/files/process', [
            'numbers' => "1234567890\n0987654321",
            'country_code' => 'US',
            'chunk_size' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'original_file' => [
                        'name',
                        'download_url',
                    ],
                    'chunks',
                    'stats' => [
                        'total',
                        'valid',
                        'invalid',
                    ],
                    'view_url',
                    'created_at',
                    'expires_at',
                ],
            ]);
    }

    public function test_get_upload_details()
    {
        $upload = Upload::create([
            'id' => 'test-id',
            'name' => 'test.txt',
            'original_path' => 'uploads/original/test-id.txt',
            'input_type' => 'text',
            'file_type' => 'txt',
            'country_code' => 'US',
            'chunk_size' => 1000,
            'chunks' => [],
            'total_numbers' => 0,
            'valid_numbers' => 0,
            'invalid_numbers' => 0,
        ]);

        $response = $this->getJson("/api/v1/files/{$upload->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'original_file' => [
                        'name',
                        'download_url',
                    ],
                    'chunks',
                    'stats' => [
                        'total',
                        'valid',
                        'invalid',
                    ],
                    'view_url',
                    'created_at',
                    'expires_at',
                ],
            ]);
    }

    public function test_download_original_file()
    {
        $upload = Upload::create([
            'id' => 'test-id',
            'name' => 'test.txt',
            'original_path' => 'uploads/original/test-id.txt',
            'input_type' => 'text',
            'file_type' => 'txt',
            'country_code' => 'US',
            'chunk_size' => 1000,
            'chunks' => [],
            'total_numbers' => 0,
            'valid_numbers' => 0,
            'invalid_numbers' => 0,
        ]);

        Storage::disk('local')->put($upload->original_path, 'test content');

        $response = $this->get("/api/v1/files/{$upload->id}/download/original");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function test_download_chunk()
    {
        $upload = Upload::create([
            'id' => 'test-id',
            'name' => 'test.txt',
            'original_path' => 'uploads/original/test-id.txt',
            'input_type' => 'text',
            'file_type' => 'txt',
            'country_code' => 'US',
            'chunk_size' => 1000,
            'chunks' => [
                ['id' => 'chunk1', 'index' => 0, 'size' => 10, 'path' => 'uploads/chunks/test-id/chunk1'],
            ],
        ]);

        Storage::disk('local')->put('uploads/chunks/test-id/chunk1', 'test content');

        $response = $this->get("/api/v1/files/{$upload->id}/chunk1/download/chunk");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function test_returns_404_for_nonexistent_upload()
    {
        $this->getJson('/api/v1/files/nonexistent-id')
            ->assertStatus(404);
    }

    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/files/process', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['country_code', 'chunk_size']);
    }
}
