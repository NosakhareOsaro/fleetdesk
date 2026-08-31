<?php

namespace App\Listeners;

use App\Contracts\CalendarSyncService;
use App\Enums\JobStatus;
use App\Events\JobStatusUpdated;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncJobToCalendar
{
    public function __construct(private CalendarSyncService $calendar) {}

    public function handle(JobStatusUpdated $event): void
    {
        if (! in_array($event->newStatus, [JobStatus::Assigned, JobStatus::Completed], true)) {
            return;
        }

        // Calendar sync is a side effect of the job transition, not part of
        // it — a Graph outage or misconfiguration must not fail the request
        // that changed the job's status.
        try {
            $this->calendar->sync($event->job);
        } catch (Throwable $e) {
            Log::error('Calendar sync failed for job '.$event->job->id, ['exception' => $e]);
        }
    }
}
