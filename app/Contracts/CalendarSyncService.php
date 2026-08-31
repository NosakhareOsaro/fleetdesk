<?php

namespace App\Contracts;

use App\Models\Job;

interface CalendarSyncService
{
    public function sync(Job $job): void;
}
