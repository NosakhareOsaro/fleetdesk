<?php

namespace Tests\Feature;

use App\Enums\JobStatus;
use App\Models\Driver;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_all_jobs_with_no_filters(): void
    {
        Job::factory()->count(3)->pending()->create();

        $jobs = app(JobService::class)->listJobs();

        $this->assertCount(3, $jobs);
    }

    public function test_it_filters_jobs_by_status(): void
    {
        Job::factory()->count(2)->pending()->create();
        Job::factory()->count(3)->completed()->create();

        $jobs = app(JobService::class)->listJobs(['status' => JobStatus::Completed]);

        $this->assertCount(3, $jobs);
        $this->assertTrue($jobs->every(fn (Job $job) => $job->status === JobStatus::Completed));
    }

    public function test_it_filters_jobs_by_driver(): void
    {
        $driver = Driver::factory()->create();
        Job::factory()->count(2)->assigned()->create(['driver_id' => $driver->id]);
        Job::factory()->count(4)->assigned()->create();

        $jobs = app(JobService::class)->listJobs(['driver_id' => $driver->id]);

        $this->assertCount(2, $jobs);
        $this->assertTrue($jobs->every(fn (Job $job) => $job->driver_id === $driver->id));
    }

    public function test_it_filters_jobs_by_status_and_driver_together(): void
    {
        $driver = Driver::factory()->create();
        Job::factory()->assigned()->create(['driver_id' => $driver->id]);
        Job::factory()->inTransit()->create(['driver_id' => $driver->id]);
        Job::factory()->count(3)->assigned()->create();

        $jobs = app(JobService::class)->listJobs([
            'status' => JobStatus::Assigned,
            'driver_id' => $driver->id,
        ]);

        $this->assertCount(1, $jobs);
    }
}
