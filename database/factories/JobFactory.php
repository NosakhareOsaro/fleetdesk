<?php

namespace Database\Factories;

use App\Enums\JobStatus;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'driver_id' => null,
            'status' => JobStatus::Pending,
            'scheduled_at' => fake()->dateTimeBetween('now', '+2 weeks'),
            'completed_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => JobStatus::Pending, 'driver_id' => null]);
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobStatus::Assigned,
            'driver_id' => Driver::factory(),
        ]);
    }

    public function inTransit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobStatus::InTransit,
            'driver_id' => Driver::factory(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobStatus::Completed,
            'driver_id' => Driver::factory(),
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => JobStatus::Cancelled]);
    }
}
