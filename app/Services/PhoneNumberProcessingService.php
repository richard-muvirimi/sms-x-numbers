<?php

namespace App\Services;

use App\Models\Upload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class PhoneNumberProcessingService
{
    private PhoneNumberUtil $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    public function processUpload(Upload $upload): Upload
    {
        $numbers = $this->extractNumbersFromFile($upload);
        $validNumbers = collect();
        $invalidNumbers = collect();

        // Process numbers
        foreach ($numbers as $number) {
            $isValid = false;
            $attempts = [
                $number,
                preg_replace('/[^0-9]/', '', $number), // Try with only digits
            ];

            // If number has more than 9 digits, also try with last 9
            $cleanNumber = preg_replace('/[^0-9]/', '', $number);
            if (strlen($cleanNumber) > 9) {
                $attempts[] = substr($cleanNumber, -9);
            }

            foreach ($attempts as $attempt) {
                try {
                    $phoneNumber = $this->phoneUtil->parse($attempt, $upload->country_code);
                    if ($this->phoneUtil->isValidNumber($phoneNumber)) {
                        $validNumbers->push($this->phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164));
                        $isValid = true;
                        break;
                    }
                } catch (NumberParseException $e) {
                    continue;
                }
            }

            if (! $isValid) {
                $invalidNumbers->push($number);
            }
        }

        // Create and save chunks
        $processedChunks = [];
        $index = 0;
        $currentChunk = collect();

        foreach ($validNumbers as $number) {
            $currentChunk->push($number);

            if ($currentChunk->count() >= $upload->chunk_size) {
                $index++;
                $filename = 'chunk_'.$upload->id.'_'.$index.'.csv';
                $path = 'uploads/chunks/'.$filename;
                $this->saveChunkToFile($currentChunk, $path);

                $id = Str::uuid();
                $processedChunks[] = [
                    'id' => $id,
                    'path' => $path,
                    'url' => url(Storage::url($path)),
                    'size' => $currentChunk->count(),
                    'index' => $index,
                ];

                $currentChunk = collect();
            }
        }

        // Save any remaining numbers in the last chunk
        if ($currentChunk->isNotEmpty()) {
            $index++;
            $filename = 'chunk_'.$upload->id.'_'.$index.'.csv';
            $path = 'uploads/chunks/'.$filename;
            $this->saveChunkToFile($currentChunk, $path);

            $id = Str::uuid();

            $processedChunks[] = [
                'id' => $id,
                'path' => $path,
                'url' => url(Storage::url($path)),
                'size' => $currentChunk->count(),
                'index' => $index,
            ];
        }

        // Update upload record
        $upload->update([
            'chunks' => $processedChunks,
            'total_numbers' => count($numbers),
            'valid_numbers' => $validNumbers->count(),
            'invalid_numbers' => $invalidNumbers->count(),
        ]);

        return $upload;
    }

    private function extractNumbersFromFile(Upload $upload): array
    {
        $numbers = [];

        if ($upload->input_type === 'text') {
            $content = Storage::get($upload->original_path);

            return preg_split('/[,\n]/', $content, -1, PREG_SPLIT_NO_EMPTY);
        }

        // Copy file from remote disk to local temporary file
        $tempPath = 'temp/' . basename($upload->original_path);
        Storage::disk('local')->writeStream($tempPath, Storage::readStream($upload->original_path));
        $filePath = Storage::disk('local')->path($tempPath);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get only the first column
        foreach ($worksheet->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator('A', 'A'); // Only column A
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $value = trim($cell->getValue());
                if (! empty($value)) {
                    $numbers[] = $value;
                }
            }
        }

        // Clean up temporary file
        Storage::disk('local')->delete($tempPath);

        return array_map('trim', $numbers);
    }

    private function saveChunkToFile(Collection $numbers, string $path): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Add header
        $sheet->setCellValue('A1', 'Phone Number');

        // Add numbers
        foreach ($numbers as $index => $number) {
            $sheet->setCellValue('A'.($index + 2), $number);
        }

        // Configure CSV writer
        $writer = new Csv($spreadsheet);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\n");
        $writer->setSheetIndex(0);
        $writer->setUseBOM(true); // Add BOM for UTF-8
        $writer->setOutputEncoding('UTF-8');

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'chunk_');
        $writer->save($tempFile);

        Storage::writeStream($path, fopen($tempFile, 'r'));
        unlink($tempFile);
    }

    public function getDownloadUrl(string $filename): string
    {
        return Storage::url('uploads/'.$filename);
    }
}
