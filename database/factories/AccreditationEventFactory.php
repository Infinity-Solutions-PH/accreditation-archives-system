<?php

namespace Database\Factories;

use App\Models\AccreditationEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccreditationEvent>
 */
class AccreditationEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'college_id' => 1,
            'program_id' => 1,
            'level' => 'Level III',
            'expires_at' => now()->addDays(30),
            'status' => 'active',
            'created_by' => 1,
        ];
    }
}
