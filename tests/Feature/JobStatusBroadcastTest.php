<?php

namespace Tests\Feature;

use App\Enums\JobStatus;
use App\Events\JobStatusUpdated;
use App\Models\Job;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobStatusBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_implements_should_broadcast_now(): void
    {
        $event = new JobStatusUpdated(new Job, JobStatus::Assigned, JobStatus::InTransit);

        $this->assertInstanceOf(ShouldBroadcastNow::class, $event);
    }

    public function test_it_broadcasts_on_a_private_jobs_channel(): void
    {
        $event = new JobStatusUpdated(new Job, JobStatus::Assigned, JobStatus::InTransit);

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertSame('private-jobs', $channels[0]->name);
    }

    public function test_it_broadcasts_the_job_id_and_statuses(): void
    {
        $job = Job::factory()->assigned()->create();

        $event = new JobStatusUpdated($job, JobStatus::Assigned, JobStatus::InTransit);

        $payload = $event->broadcastWith();

        $this->assertSame($job->id, $payload['id']);
        $this->assertSame('assigned', $payload['previousStatus']);
        $this->assertSame('in_transit', $payload['status']);
    }
}
