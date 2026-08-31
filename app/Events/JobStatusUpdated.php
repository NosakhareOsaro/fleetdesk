<?php

namespace App\Events;

use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Broadcasts synchronously (ShouldBroadcastNow, not ShouldBroadcast) since
// this app has no queue worker running — queuing it would mean it's never
// actually delivered.
class JobStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Job $job,
        public readonly JobStatus $previousStatus,
        public readonly JobStatus $newStatus,
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('jobs')];
    }

    public function broadcastAs(): string
    {
        return 'job.status-updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->job->id,
            'previousStatus' => $this->previousStatus->value,
            'status' => $this->newStatus->value,
        ];
    }
}
