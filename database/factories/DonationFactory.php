<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        $types = ['Cash', 'Food', 'Clothing', 'Medical Supplies', 'School Supplies', 'Construction Materials'];
        $type = fake()->randomElement($types);
        
        return [
            'donor_id' => User::factory()->donor(),
            'type' => $type,
            'quantity' => fake()->randomFloat(2, 10, 1000),
            'value' => fake()->randomFloat(2, 500, 50000),
            'destination' => fake()->optional()->randomElement(['General', 'Purok 5', 'Fire Victims', 'Flood Relief']),
            'status' => fake()->randomElement(['Pending', 'Verified', 'Distributed']),
            'verified_by' => null,
            'verified_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pending',
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    public function verified()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Verified',
            'verified_by' => User::factory()->official(),
            'verified_at' => now(),
        ]);
    }

    public function distributed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Distributed',
            'verified_by' => User::factory()->official(),
            'verified_at' => now(),
        ]);
    }
}