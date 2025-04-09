<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ProcessNumbersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numbers' => ['required_without:file', 'string', 'nullable'],
            'file' => ['required_without:numbers', 'file', 'mimes:csv,xlsx,xls,txt', 'nullable'],
            'country_code' => ['required', 'string', 'size:2'],
            'chunk_size' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('file') && $this->filled('numbers')) {
                $validator->errors()->add('file', 'Please provide either phone numbers or upload a file, not both.');
                $validator->errors()->add('numbers', 'Please provide either phone numbers or upload a file, not both.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'numbers.required_without' => 'Please provide either phone numbers or upload a file.',
            'numbers.prohibited_if' => 'Please provide either phone numbers or upload a file, not both.',
            'file.required_without' => 'Please provide either phone numbers or upload a file.',
            'file.prohibited_if' => 'Please provide either phone numbers or upload a file, not both.',
            'file.mimes' => 'The file must be a CSV, Excel, or text file.',
            'country_code.required' => 'Please select a country code.',
            'country_code.size' => 'The country code must be 2 characters long.',
            'chunk_size.required' => 'Please specify a chunk size.',
            'chunk_size.integer' => 'The chunk size must be a number.',
            'chunk_size.min' => 'The chunk size must be at least 1.',
            'chunk_size.max' => 'The chunk size cannot exceed 10,000.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Log the request data
        Log::info('Request data before preparation:', [
            'has_file' => $this->hasFile('file'),
            'has_numbers' => $this->has('numbers'),
            'all_data' => $this->all(),
        ]);

        // Log after preparation
        Log::info('Request data after preparation:', [
            'has_file' => $this->hasFile('file'),
            'has_numbers' => $this->has('numbers'),
            'all_data' => $this->all(),
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['numbers'])) {
            $validated['numbers'] = array_filter(
                preg_split('/[,\n]/', $validated['numbers'], -1, PREG_SPLIT_NO_EMPTY),
                'trim'
            );
        }

        return $validated;
    }
}
