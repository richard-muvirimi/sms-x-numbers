<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Upload extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'name',
        'original_path',
        'chunks',
        'input_type',
        'file_type',
        'country_code',
        'chunk_size',
        'total_numbers',
        'valid_numbers',
        'invalid_numbers',
        'expires_at',
    ];

    protected $casts = [
        'chunks' => 'array',
        'expires_at' => 'datetime',
        'total_numbers' => 'integer',
        'valid_numbers' => 'integer',
        'invalid_numbers' => 'integer',
        'chunk_size' => 'integer',
    ];
}
