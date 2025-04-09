<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CountryService
{
    private const CACHE_TTL = 86400; // 24 hours

    private const COUNTRY_IO_BASE_URL = 'http://country.io';

    public function getCountryCodes(): array
    {
        return Cache::remember('country_codes', self::CACHE_TTL, function () {
            $response = Http::get(self::COUNTRY_IO_BASE_URL.'/phone.json');

            if ($response->successful()) {
                $countries = $response->json();
                $formatted = [];

                foreach ($countries as $code => $prefix) {
                    // Format the prefix to ensure it starts with '+'
                    $formattedPrefix = '+'.ltrim($prefix, '+');
                    $formatted[$code] = [
                        'code' => $code,
                        'prefix' => $formattedPrefix,
                    ];
                }

                return $formatted;
            }

            return [];
        });
    }

    public function getCountryNames(): array
    {
        return Cache::remember('country_names', self::CACHE_TTL, function () {
            $response = Http::get(self::COUNTRY_IO_BASE_URL.'/names.json');

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        });
    }

    public function getAllCountryData(): array
    {
        $codes = $this->getCountryCodes();
        $names = $this->getCountryNames();
        $merged = [];

        foreach ($codes as $code => $data) {
            if (isset($names[$code])) {
                $merged[] = [
                    'code' => $code,
                    'name' => $names[$code],
                    'prefix' => $data['prefix'],
                ];
            }
        }

        // Sort by name
        usort($merged, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $merged;
    }
}
