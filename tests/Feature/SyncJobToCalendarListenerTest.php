<?php

namespace Tests\Feature;

use App\Contracts\CalendarSyncService;
use App\Enums\JobStatus;
use App\Events\JobStatusUpdated;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncJobToCalendarListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_to_the_calendar_when_a_job_is_assigned(): void
    {
        $job = Job::factory()->assigned()->create();

        $this->mock(CalendarSyncService::class)
            ->shouldReceive('sync')
            ->once()
            ->with(\Mockery::on(fn (Job $synced) => $synced->is($job)));

        event(new JobStatusUpdated($job, JobStatus::Pending, JobStatus::Assigned));
    }

    public function test_it_syncs_to_the_calendar_when_a_job_is_completed(): void
    {
        $job = Job::factory()->completed()->create();

        $this->mock(CalendarSyncService::class)
            ->shouldReceive('sync')
            ->once()
            ->with(\Mockery::on(fn (Job $synced) => $synced->is($job)));

        event(new JobStatusUpdated($job, JobStatus::InTransit, JobStatus::Completed));
    }

    public function test_it_does_not_sync_for_other_transitions(): void
    {
        $job = Job::factory()->inTransit()->create();

        $this->mock(CalendarSyncService::class)->shouldNotReceive('sync');

        event(new JobStatusUpdated($job, JobStatus::Assigned, JobStatus::InTransit));
    }
}
