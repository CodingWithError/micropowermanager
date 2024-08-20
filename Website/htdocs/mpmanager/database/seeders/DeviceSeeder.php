<?php

namespace Database\Seeders;

use App\Models\ConnectionGroup;
use App\Models\ConnectionType;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Person\Person;
use Illuminate\Database\Seeder;
use MPM\DatabaseProxy\DatabaseProxyManagerService;

const DUMMY_COMPANY_ID = 1;

class DeviceSeeder extends Seeder
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
        // Manufacturer
        // For now, we just adding some dummy Manufacturers.
        // Later, this should probably be synced with the manufacturers
        // for which we have plugins in the Demo setup.
        $manufacturers = Manufacturer::factory()
            ->count(3)
            ->isMeterManufacturer()
            ->create();

        // Connection Group / Connection Type
        ConnectionType::create(['name' => 'House Hold']);
        ConnectionType::create(['name' => 'Commercial Usage']);
        ConnectionType::create(['name' => 'Productive Usage']);
        ConnectionType::create(['name' => 'Residential']);
        ConnectionType::create(['name' => 'Business']);
        ConnectionType::create(['name' => 'Institution']);
        ConnectionType::create(['name' => 'Not Specified']);

        ConnectionGroup::create(['name' => 'Household']);
        ConnectionGroup::create(['name' => 'Community']);
        ConnectionGroup::create(['name' => 'Cafeterias and Pubs']);
        ConnectionGroup::create(['name' => 'Hair Dressers']);
        ConnectionGroup::create(['name' => 'Churches & Mosques']);
        ConnectionGroup::create(['name' => 'Solar Excess']);
        ConnectionGroup::create(['name' => 'H.H Cus']);
        ConnectionGroup::create(['name' => 'FOOD AND BEVERAGES']);
        ConnectionGroup::create(['name' => 'SHOPS w']);
        ConnectionGroup::create(['name' => 'SCHOOLS']);
        ConnectionGroup::create(['name' => 'HEALTH CENTRES & INSTITUTIONS']);
        ConnectionGroup::create(['name' => 'GUEST HOUSES']);
        ConnectionGroup::create(['name' => 'ENTERTAINMENT CENTRES']);
        ConnectionGroup::create(['name' => 'Mobile Charging, OFFICES and Stationery']);
        ConnectionGroup::create(['name' => 'Small scale HATCHERIES']);
        ConnectionGroup::create(['name' => 'Laundry']);
        ConnectionGroup::create(['name' => 'Freezing Units']);
        ConnectionGroup::create(['name' => 'Wood work']);
        ConnectionGroup::create(['name' => 'Mills']);
        ConnectionGroup::create(['name' => 'Welders']);
        ConnectionGroup::create(['name' => 'Drinking Water Project']);
        ConnectionGroup::create(['name' => 'Bakery']);
        ConnectionGroup::create(['name' => 'Not ordered yet']);

        // Tariff

        // Actual Meters

        // Get available customers
        $persons = Person::all();

        $meters = Meter::factory()
            ->count(1)
            ->make();
    }
}
