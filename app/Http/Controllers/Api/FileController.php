<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessNumbersRequest;
use App\Models\Upload;
use App\Services\FileUploadService;
use App\Services\PhoneNumberProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private PhoneNumberProcessingService $phoneNumberProcessingService,
    ) {}

    public function processUpload(ProcessNumbersRequest $request): JsonResponse
    {
        try {
            // Create upload record
            $upload = $this->fileUploadService->handleUpload(
                $request->hasFile('file') ? $request->file('file') : null,
                $request->input('numbers'),
                $request->input('country_code'),
                $request->input('chunk_size')
            );

            // Process the numbers
            $processedUpload = $this->phoneNumberProcessingService->processUpload($upload);

            return response()->json([
                'success' => true,
                'message' => 'Numbers processed successfully',
                'data' => $this->formatUploadResponse($processedUpload),
            ]);
        } catch (\Exception $e) {
            Log::error('File upload error: '.$e->getMessage().'\n'.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function formatUploadResponse(Upload $upload): array
    {
        return [
            'id' => $upload->id,
            'original_file' => [
                'name' => $upload->name,
                'download_url' => route('api.files.download', ['upload' => $upload->id]),
            ],
            'chunks' => array_map(function ($chunk) use ($upload) {
                return [
                    'id' => $chunk['id'],
                    'download_url' => route('api.files.download.chunk', ['upload' => $upload->id, 'chunk' => $chunk['id']]),
                    'size' => $chunk['size'],
                    'index' => $chunk['index'],
                ];
            }, $upload->chunks ?? []),
            'stats' => [
                'total' => $upload->total_numbers,
                'valid' => $upload->valid_numbers,
                'invalid' => $upload->invalid_numbers,
            ],
            'view_url' => route('api.files.view', ['upload' => $upload->id]),
            'created_at' => $upload->created_at,
            'expires_at' => $upload->created_at->addDays((int) config('upload.max_age_days', 30)),
        ];
    }

    public function getUpload(Upload $upload): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Upload retrieved successfully',
                'data' => $this->formatUploadResponse($upload),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadUpload(Upload $upload)
    {
        $path = $upload->original_path;
        if (! Storage::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        return Storage::download($path, $upload->name);
    }

    public function downloadChunk(Upload $upload, string $chunk)
    {
        $chunkData = collect($upload->chunks)->firstWhere('id', $chunk);

        if (! $chunkData) {
            return response()->json([
                'success' => false,
                'message' => 'Chunk not found',
            ], 404);
        }

        $path = $chunkData['path'];
        if (! Storage::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Chunk file not found',
            ], 404);
        }

        return Storage::download($path, $chunkData['id'].'.csv');
    }
}
