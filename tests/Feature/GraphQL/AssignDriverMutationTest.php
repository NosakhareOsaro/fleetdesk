<?php

namespace Tests\Feature\GraphQL;

use App\Models\Driver;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class AssignDriverMutationTest extends TestCase
{
    use MakesGraphQLRequests, RefreshDatabase;

    public function test_it_assigns_a_driver_to_a_pending_job(): void
    {
        $job = Job::factory()->pending()->create();
        $driver = Driver::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation ($jobId: ID!, $driverId: ID!) {
                assignDriver(jobId: $jobId, driverId: $driverId) {
                    id
                    status
                    driver {
                        id
                    }
                }
            }
        ', ['jobId' => $job->id, 'driverId' => $driver->id]);

        $response->assertJsonPath('data.assignDriver.status', 'ASSIGNED');
        $response->assertJsonPath('data.assignDriver.driver.id', (string) $driver->id);
    }

    public function test_it_returns_a_client_safe_error_for_a_non_pending_job(): void
    {
        $job = Job::factory()->completed()->create();
        $driver = Driver::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation ($jobId: ID!, $driverId: ID!) {
                assignDriver(jobId: $jobId, driverId: $driverId) {
                    id
                }
            }
        ', ['jobId' => $job->id, 'driverId' => $driver->id]);

        $response->assertJsonPath('errors.0.message', 'Cannot transition job from [completed] to [assigned].');
    }
}
