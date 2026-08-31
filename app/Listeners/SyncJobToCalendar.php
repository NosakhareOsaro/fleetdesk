<?php

namespace App\Listeners;

use App\Contracts\CalendarSyncService;
use App\Enums\JobStatus;
use App\Events\JobStatusUpdated;

class SyncJobToCalendar
{
    public function __construct(private CalendarSyncService $calendar) {}

    public function handle(JobStatusUpdated $event): void
    {
        if (! in_array($event->newStatus, [JobStatus::Assigned, JobStatus::Completed], true)) {
            return;
        }

        $this->calendar->sync($event->job);
    }
}
