<?php

namespace App\Events;

use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Job $job,
        public readonly JobStatus $previousStatus,
        public readonly JobStatus $newStatus,
    ) {}
}
