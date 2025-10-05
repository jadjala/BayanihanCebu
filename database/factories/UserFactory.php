<?php
// database/factories/UserFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => fake()->randomElement(['resident', 'donor', 'official']),
            'contact_info' => fake()->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }

    public function resident()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'resident',
        ]);
    }

    public function donor()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'donor',
        ]);
    }

    public function official()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'official',
        ]);
    }

    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}