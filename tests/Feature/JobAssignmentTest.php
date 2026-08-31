<?php

namespace Tests\Feature;

use App\Enums\JobStatus;
use App\Exceptions\InvalidJobTransitionException;
use App\Models\Driver;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_assigns_a_driver_to_a_pending_job(): void
    {
        $job = Job::factory()->pending()->create();
        $driver = Driver::factory()->create();

        $updated = app(JobService::class)->assignDriver($job, $driver);

        $this->assertSame($driver->id, $updated->driver_id);
        $this->assertSame(JobStatus::Assigned, $updated->status);
        $this->assertDatabaseHas('delivery_jobs', [
            'id' => $job->id,
            'driver_id' => $driver->id,
            'status' => JobStatus::Assigned->value,
        ]);
    }

    public function test_it_rejects_assigning_a_driver_to_a_job_that_is_not_pending(): void
    {
        $job = Job::factory()->completed()->create();
        $driver = Driver::factory()->create();

        $this->expectException(InvalidJobTransitionException::class);

        app(JobService::class)->assignDriver($job, $driver);
    }
}
