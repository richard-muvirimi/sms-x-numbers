<?php

namespace App\Services;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function handleUpload(?UploadedFile $file, ?string $textContent, string $countryCode, int $chunkSize): Upload
    {
        $id = Str::uuid();
        $uploadData = [
            'id' => $id,
            'country_code' => $countryCode,
            'chunk_size' => $chunkSize,
            'chunks' => [],
            'total_numbers' => 0,
            'valid_numbers' => 0,
            'invalid_numbers' => 0,
        ];

        // Ensure storage directories exist
        Storage::makeDirectory('uploads/original');
        Storage::makeDirectory('uploads/chunks');

        if ($file) {
            $extension = $file->getClientOriginalExtension() ?: 'txt';
            $path = 'uploads/original/'.$id.'.'.$extension;
            Storage::putFileAs('uploads/original', $file, $id.'.'.$extension);

            $uploadData = array_merge($uploadData, [
                'name' => $file->getClientOriginalName(),
                'original_path' => $path,
                'input_type' => 'file',
                'file_type' => $extension,
            ]);
        } else {
            // Handle text input
            $path = 'uploads/original/'.$id.'.txt';
            Storage::put($path, $textContent);

            $uploadData = array_merge($uploadData, [
                'name' => $id.'.txt',
                'original_path' => $path,
                'input_type' => 'text',
                'file_type' => 'txt',
            ]);
        }

        return Upload::create($uploadData);
    }

    public function getUpload(Upload $upload): array
    {
        return [
            'id' => $upload->id,
            'original_file' => [
                'name' => $upload->name,
                'download_url' => route('api.files.download', ['upload' => $upload->id]),
            ],
            'chunks' => $upload->chunks,
            'stats' => [
                'total' => $upload->total_numbers,
                'valid' => $upload->valid_numbers,
                'invalid' => $upload->invalid_numbers,
            ],
            'created_at' => $upload->created_at,
        ];
    }

    public function deleteExpiredFiles(): void
    {
        $expiredUploads = Upload::where('created_at', '<', now()->subDays((int) config('upload.max_age_days', 30)))->get();

        foreach ($expiredUploads as $upload) {
            // Delete original file if it exists
            Storage::delete($upload->original_path);

            // Delete chunk files if they exist
            foreach ($upload->chunks as $chunk) {
                if (isset($chunk['path'])) {
                    Storage::delete($chunk['path']);
                }
            }

            // Delete the upload record
            $upload->delete();
        }
    }
}
