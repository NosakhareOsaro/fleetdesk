<?php

namespace App\GraphQL\Mutations;

use App\Enums\JobStatus;
use App\Models\Job;
use App\Services\JobService;

class UpdateJobStatus
{
    public function __construct(private JobService $jobs) {}

    public function __invoke($_, array $args): Job
    {
        $job = Job::findOrFail($args['jobId']);

        return $this->jobs->updateStatus($job, JobStatus::from($args['status']));
    }
}
