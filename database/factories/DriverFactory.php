<?php

namespace Database\Factories;

use App\Enums\DriverStatus;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->driver(),
            'status' => DriverStatus::Available,
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => ['status' => DriverStatus::Available]);
    }

    public function onJob(): static
    {
        return $this->state(fn (array $attributes) => ['status' => DriverStatus::OnJob]);
    }

    public function offDuty(): static
    {
        return $this->state(fn (array $attributes) => ['status' => DriverStatus::OffDuty]);
    }
}
