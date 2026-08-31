<?php

namespace App\Services;

use App\Enums\JobStatus;
use App\Events\JobStatusUpdated;
use App\Exceptions\InvalidJobTransitionException;
use App\Models\Driver;
use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;

class JobService
{
    public function createJob(array $data): Job
    {
        return Job::create([
            'customer_id' => $data['customer_id'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => JobStatus::Pending,
        ]);
    }

    public function assignDriver(Job $job, Driver $driver): Job
    {
        $this->transition($job, JobStatus::Assigned);

        $job->driver_id = $driver->id;
        $job->save();

        return $job;
    }

    public function updateStatus(Job $job, JobStatus $status): Job
    {
        return $this->transition($job, $status);
    }

    public function listJobs(array $filters = []): Collection
    {
        return Job::query()
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['driver_id']), fn ($query) => $query->where('driver_id', $filters['driver_id']))
            ->get();
    }

    private function transition(Job $job, JobStatus $status): Job
    {
        $previousStatus = $job->status;

        if (! $previousStatus->canTransitionTo($status)) {
            throw InvalidJobTransitionException::from($previousStatus, $status);
        }

        $job->status = $status;

        if ($status === JobStatus::Completed) {
            $job->completed_at = now();
        }

        $job->save();

        event(new JobStatusUpdated($job, $previousStatus, $status));

        return $job;
    }
}
