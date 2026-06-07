<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'original_filename' => $this->faker->word . '.pdf',
            'path' => 'files/' . Str::random(10) . '.pdf',
            'title' => $this->faker->sentence(3),
            'extension' => 'pdf',
            'size' => rand(1000, 5000),
            'status' => 'completed',
            'is_general' => true,
            'uploaded_by' => 1,
            'college_id' => 1,
            'program_id' => 1,
        ];
    }
}
