<?php

namespace Tests\Feature\GraphQL;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class UpdateJobStatusMutationTest extends TestCase
{
    use MakesGraphQLRequests, RefreshDatabase;

    public function test_it_moves_an_assigned_job_to_in_transit(): void
    {
        $job = Job::factory()->assigned()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation ($jobId: ID!, $status: JobStatus!) {
                updateJobStatus(jobId: $jobId, status: $status) {
                    status
                }
            }
        ', ['jobId' => $job->id, 'status' => 'IN_TRANSIT']);

        $response->assertJsonPath('data.updateJobStatus.status', 'IN_TRANSIT');
    }

    public function test_it_rejects_an_invalid_transition(): void
    {
        $job = Job::factory()->pending()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation ($jobId: ID!, $status: JobStatus!) {
                updateJobStatus(jobId: $jobId, status: $status) {
                    status
                }
            }
        ', ['jobId' => $job->id, 'status' => 'COMPLETED']);

        $response->assertJsonPath('errors.0.message', 'Cannot transition job from [pending] to [completed].');
    }
}
