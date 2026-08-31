<?php

namespace App\Services\Graph;

use App\Contracts\CalendarSyncService;
use App\Models\Job;

// Default binding outside of tests that specifically exercise calendar
// sync — keeps the rest of the suite from making real Graph API calls
// every time a job is assigned or completed.
class NullCalendarSyncService implements CalendarSyncService
{
    public function sync(Job $job): void
    {
        //
    }
}
