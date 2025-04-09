<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Here you can configure various settings related to file uploads
    |
    */

    'max_size' => env('UPLOAD_MAX_SIZE', 10485760), // 10MB in bytes
    'max_age_days' => env('MAX_FILE_AGE_DAYS', 30),
    'allowed_types' => [
        'csv' => 'text/csv',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ],
    'storage_path' => 'uploads',
];
