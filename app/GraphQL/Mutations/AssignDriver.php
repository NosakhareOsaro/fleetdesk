<?php

namespace App\GraphQL\Mutations;

use App\Models\Driver;
use App\Models\Job;
use App\Services\JobService;

class AssignDriver
{
    public function __construct(private JobService $jobs) {}

    public function __invoke($_, array $args): Job
    {
        $job = Job::findOrFail($args['jobId']);
        $driver = Driver::findOrFail($args['driverId']);

        return $this->jobs->assignDriver($job, $driver);
    }
}
