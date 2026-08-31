<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeedCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_a_dispatcher_drivers_customers_and_jobs(): void
    {
        $this->artisan('demo:seed')->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'dispatcher@fleetdesk.test']);
        $this->assertSame(4, Driver::count());
        $this->assertSame(6, Customer::count());
        $this->assertSame(13, Job::count());
    }

    public function test_it_is_safe_to_run_twice(): void
    {
        $this->artisan('demo:seed')->assertSuccessful();
        $this->artisan('demo:seed')->assertSuccessful();

        $this->assertSame(1, User::where('email', 'dispatcher@fleetdesk.test')->count());
        $this->assertSame(4, Driver::count());
        $this->assertSame(6, Customer::count());
    }
}
