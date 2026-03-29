<?php

namespace Database\Factories;

use App\Models\Accreditor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Accreditor>
 */
class AccreditorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'expires_at' => now()->addDays(30),
            'college_id' => 1,
            'program_id' => 1,
            'level' => 'Level III',
            'created_by' => 1,
        ];
    }
}
