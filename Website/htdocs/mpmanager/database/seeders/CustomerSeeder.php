<?php

namespace Database\Seeders;

use App\Models\Address\Address;
use App\Models\City;
use App\Models\GeographicalInformation;
use App\Models\Person\Person;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use MPM\DatabaseProxy\DatabaseProxyManagerService;

const DUMMY_COMPANY_ID = 1;

class CustomerSeeder extends Seeder
{
    public function __construct(
        private DatabaseProxyManagerService $databaseProxyManagerService,
    ) {
        $this->databaseProxyManagerService->buildDatabaseConnectionByCompanyId(DUMMY_COMPANY_ID);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get available Villages
        $villages = City::all();

        // For each Village generate customers
        foreach ($villages as $village) {
            Person::factory()
            ->count(50)
            ->isCustomer()
            ->has(
                Address::factory()
                    ->for($village)
                    ->has(
                        GeographicalInformation::factory()
                            ->state(function (array $attributes, Address $address) {
                                return ['points' => $address->city->location->points];
                            })
                            ->randomizePoints(),
                        'geo'
                    )
            )
            ->create();
        }
    }
}
