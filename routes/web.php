<?php

use App\Http\Controllers\PageController;
use App\Models\Upload;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index');
});

Route::get('/uploads/{upload}', function (Upload $upload) {
    return Inertia::render('Show', [
        'upload' => [
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
            }, $upload->chunks),
            'stats' => [
                'total' => $upload->total_numbers,
                'valid' => $upload->valid_numbers,
                'invalid' => $upload->invalid_numbers,
            ],
            'created_at' => $upload->created_at,
            'expires_at' => $upload->created_at->addDays((int) config('upload.max_age_days', 30)),
        ],
    ]);
})->name('uploads.show');

Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/developers', [PageController::class, 'developers'])->name('developers');
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
