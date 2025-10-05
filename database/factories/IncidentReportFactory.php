<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\IncidentReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentReportFactory extends Factory
{
    protected $model = IncidentReport::class;

    public function definition(): array
    {
        $categories = ['Fire', 'Flood', 'Medical Emergency', 'Crime', 'Accident', 'Infrastructure', 'Noise Complaint', 'Other'];
        $category = fake()->randomElement($categories);
        $description = $this->generateDescription($category);
        
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => $description,
            'category' => $category,
            'location' => 'Purok ' . fake()->numberBetween(1, 10) . ', Brgy. ' . fake()->city(),
            'photo_path' => null,
            'urgency_level' => IncidentReport::detectUrgency($description, $category),
            'status' => fake()->randomElement(['Pending', 'Verified', 'Resolved']),
            'official_comment' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];
    }

    private function generateDescription($category): string
    {
        $descriptions = [
            'Fire' => 'There is a fire incident at the location. Immediate assistance needed.',
            'Flood' => 'Heavy flooding in the area due to continuous rain. Multiple houses affected.',
            'Medical Emergency' => 'Medical emergency reported. Patient needs immediate attention.',
            'Crime' => 'Suspicious activity reported in the neighborhood.',
            'Accident' => 'Traffic accident occurred with possible injuries.',
            'Infrastructure' => 'Road damage reported affecting local transportation.',
            'Noise Complaint' => 'Excessive noise disturbance during late hours.',
            'Other' => 'General incident requiring official attention.',
        ];

        return $descriptions[$category] ?? 'Incident reported in the area.';
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

    public function resolved()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Resolved',
            'verified_by' => User::factory()->official(),
            'verified_at' => now(),
            'official_comment' => fake()->sentence(),
        ]);
    }
}