<?php

namespace Tests\Feature;

use App\Enums\JobStatus;
use App\Models\Customer;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_job_for_a_customer_in_pending_status(): void
    {
        $customer = Customer::factory()->create();

        $job = app(JobService::class)->createJob([
            'customer_id' => $customer->id,
            'scheduled_at' => '2026-09-15 09:00:00',
            'notes' => 'Deliver 5 drums of waste oil.',
        ]);

        $this->assertDatabaseHas('delivery_jobs', [
            'id' => $job->id,
            'customer_id' => $customer->id,
            'status' => JobStatus::Pending->value,
            'notes' => 'Deliver 5 drums of waste oil.',
        ]);

        $this->assertSame(JobStatus::Pending, $job->status);
        $this->assertNull($job->driver_id);
    }
}
