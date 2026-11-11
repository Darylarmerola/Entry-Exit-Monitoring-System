<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => null,
            'name' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'age' => fake()->numberBetween(18, 30),
            'course' => fake()->randomElement([
                'BSIT',
                'BSCS',
                'BSN',
                'BSCE'
            ]),
            'gender' => fake()->randomElement([
                'Male',
                'Female'
            ]),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function student(): static
{
    return $this->state(fn () => [
        'role' => 'student',
    ]);
}


    public function teacher(): static
    {
        return $this->state(fn () => [
            'role' => 'teacher',
            'course' => null,
        ]);
    }
}