<?php

namespace App\Console\Commands;

use App\Services\FileUploadService;
use Illuminate\Console\Command;

class CleanupExpiredFiles extends Command
{
    protected $signature = 'app:cleanup';

    protected $description = 'Clean up expired files';

    private $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        parent::__construct();
        $this->fileUploadService = $fileUploadService;
    }

    public function handle()
    {
        $this->info('Starting cleanup of expired files...');
        try {
            $this->fileUploadService->deleteExpiredFiles();
            $this->info('Cleanup completed successfully.');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error during cleanup: '.$e->getMessage());

            return 1;
        }
    }
}
