<?php

use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Country routes
    Route::get('countries', [CountryController::class, 'index']);

    // File processing routes
    Route::post('files/process', [FileController::class, 'processUpload']);
    Route::get('files/{upload}', [FileController::class, 'getUpload'])->name('api.files.view');
    Route::get('files/{upload}/download/original', [FileController::class, 'downloadUpload'])->name('api.files.download');
    Route::get('files/{upload}/{chunk}/download/chunk', [FileController::class, 'downloadChunk'])->name('api.files.download.chunk');
});
