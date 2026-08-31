<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Job;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DemoSeedCommand extends Command
{
    protected $signature = 'demo:seed';

    protected $description = 'Reset the database and seed realistic demo data for exploring FleetDesk';

    /** @var list<string> */
    private array $customerNames = [
        'Northgate Fuel Depot',
        'Harbor Waste Ltd',
        'Crownfield Heating Oil',
        'Riverside Skip Hire',
        'Milltown Biofuels',
        'Oakhurst Industrial Waste',
    ];

    public function handle(): int
    {
        $this->components->info('Clearing existing demo data...');

        Job::query()->delete();
        User::whereIn('id', Driver::pluck('user_id'))->delete();
        Driver::query()->delete();
        Customer::query()->delete();

        $this->components->task('Dispatcher account', function () {
            User::updateOrCreate(
                ['email' => 'dispatcher@fleetdesk.test'],
                ['name' => 'Dana Dispatcher', 'password' => Hash::make('password'), 'role' => UserRole::Dispatcher],
            );

            return true;
        });

        $drivers = collect();
        $this->components->task('Drivers', function () use (&$drivers) {
            $drivers = Driver::factory()->count(4)->create();

            return true;
        });

        $customers = collect();
        $this->components->task('Customers', function () use (&$customers) {
            $customers = collect($this->customerNames)
                ->map(fn (string $name) => Customer::factory()->create(['name' => $name]));

            return true;
        });

        $this->components->task('Jobs', function () use ($drivers, $customers) {
            Job::factory()->count(3)->pending()->recycle($customers)->create();
            Job::factory()->count(3)->assigned()->recycle($customers)->recycle($drivers)->create();
            Job::factory()->count(2)->inTransit()->recycle($customers)->recycle($drivers)->create();
            Job::factory()->count(4)->completed()->recycle($customers)->recycle($drivers)->create();
            Job::factory()->count(1)->cancelled()->recycle($customers)->create();

            return true;
        });

        $this->newLine();
        $this->components->info('Demo data seeded. Log in with dispatcher@fleetdesk.test / password.');

        return self::SUCCESS;
    }
}
