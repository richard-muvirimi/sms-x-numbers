<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('original_path')->nullable();  // Path to the original uploaded file
            $table->string('name');                      // Original filename
            $table->json('chunks');                      // Array of chunk file information
            $table->string('input_type');               // 'file' or 'text'
            $table->string('file_type')->nullable();    // 'csv', 'xlsx', etc.
            $table->string('country_code');             // Country code used for normalization
            $table->integer('chunk_size');              // Size of chunks used
            $table->integer('total_numbers')->default(0);           // Total numbers processed
            $table->integer('valid_numbers')->default(0);           // Count of valid numbers
            $table->integer('invalid_numbers')->default(0);         // Count of invalid numbers
            // When the files will be deleted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
