<?php

namespace Database\Factories;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UploadFactory extends Factory
{
    protected $model = Upload::class;

    public function definition(): array
    {
        $id = Str::uuid();

        return [
            'id' => $id,
            'name' => $this->faker->randomElement(['numbers.csv', 'phones.txt', 'contacts.xlsx']),
            'original_path' => 'uploads/original/'.$id.'.txt',
            'input_type' => $this->faker->randomElement(['file', 'text']),
            'file_type' => $this->faker->randomElement(['csv', 'txt', 'xlsx']),
            'country_code' => $this->faker->countryCode(),
            'chunk_size' => $this->faker->numberBetween(100, 1000),
            'total_numbers' => $this->faker->numberBetween(1000, 5000),
            'valid_numbers' => function (array $attributes) {
                return $this->faker->numberBetween(0, $attributes['total_numbers']);
            },
            'invalid_numbers' => function (array $attributes) {
                return $attributes['total_numbers'] - $attributes['valid_numbers'];
            },
            'chunks' => function (array $attributes) {
                $chunks = [];
                $remaining = $attributes['total_numbers'];
                $index = 0;
                while ($remaining > 0) {
                    $size = min($remaining, $attributes['chunk_size']);
                    $chunks[] = [
                        'id' => 'chunk'.$index,
                        'index' => $index,
                        'size' => $size,
                        'path' => 'uploads/chunks/'.$attributes['id'].'.chunk'.$index,
                    ];
                    $remaining -= $size;
                    $index++;
                }

                return $chunks;
            },

        ];
    }
}
