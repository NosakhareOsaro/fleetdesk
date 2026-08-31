<?php

namespace Tests\Feature;

use App\Enums\JobStatus;
use App\Events\JobStatusUpdated;
use App\Exceptions\InvalidJobTransitionException;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class JobStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transitions_an_assigned_job_to_in_transit(): void
    {
        $job = Job::factory()->assigned()->create();

        $updated = app(JobService::class)->updateStatus($job, JobStatus::InTransit);

        $this->assertSame(JobStatus::InTransit, $updated->status);
    }

    public function test_it_transitions_an_in_transit_job_to_completed_and_stamps_completed_at(): void
    {
        $job = Job::factory()->inTransit()->create();

        $updated = app(JobService::class)->updateStatus($job, JobStatus::Completed);

        $this->assertSame(JobStatus::Completed, $updated->status);
        $this->assertNotNull($updated->completed_at);
    }

    public function test_it_dispatches_job_status_updated_event_on_a_valid_transition(): void
    {
        Event::fake();

        $job = Job::factory()->assigned()->create();

        app(JobService::class)->updateStatus($job, JobStatus::InTransit);

        Event::assertDispatched(JobStatusUpdated::class, fn (JobStatusUpdated $event) => $event->job->is($job)
            && $event->previousStatus === JobStatus::Assigned
            && $event->newStatus === JobStatus::InTransit);
    }

    public function test_it_rejects_a_pending_job_jumping_straight_to_completed(): void
    {
        $job = Job::factory()->pending()->create();

        $this->expectException(InvalidJobTransitionException::class);

        app(JobService::class)->updateStatus($job, JobStatus::Completed);
    }

    public function test_it_rejects_transitioning_a_completed_job(): void
    {
        $job = Job::factory()->completed()->create();

        $this->expectException(InvalidJobTransitionException::class);

        app(JobService::class)->updateStatus($job, JobStatus::InTransit);
    }

    public function test_it_rejects_transitioning_a_cancelled_job(): void
    {
        $job = Job::factory()->cancelled()->create();

        $this->expectException(InvalidJobTransitionException::class);

        app(JobService::class)->updateStatus($job, JobStatus::Assigned);
    }
}
