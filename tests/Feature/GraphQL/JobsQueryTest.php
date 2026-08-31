<?php

namespace Tests\Feature\GraphQL;

use App\Models\Driver;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class JobsQueryTest extends TestCase
{
    use MakesGraphQLRequests, RefreshDatabase;

    public function test_it_lists_jobs_with_their_customer(): void
    {
        Job::factory()->count(3)->pending()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query {
                jobs {
                    id
                    status
                    customer {
                        name
                    }
                }
            }
        ')->assertJsonCount(3, 'data.jobs');
    }

    public function test_it_filters_jobs_by_status(): void
    {
        Job::factory()->count(2)->pending()->create();
        Job::factory()->count(1)->completed()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                jobs(status: COMPLETED) {
                    id
                    status
                }
            }
        ');

        $response->assertJsonCount(1, 'data.jobs');
        $response->assertJsonPath('data.jobs.0.status', 'COMPLETED');
    }

    public function test_it_filters_jobs_by_driver(): void
    {
        $driver = Driver::factory()->create();
        Job::factory()->assigned()->create(['driver_id' => $driver->id]);
        Job::factory()->count(2)->assigned()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            query ($driverId: ID!) {
                jobs(driverId: $driverId) {
                    id
                }
            }
        ', ['driverId' => $driver->id]);

        $response->assertJsonCount(1, 'data.jobs');
    }
}
