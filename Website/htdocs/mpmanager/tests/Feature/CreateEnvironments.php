<?php

namespace Tests\Feature;

use App\Models\Address\Address;
use App\Models\GeographicalInformation;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Services\AgentBalanceHistoryService;
use Database\Factories\AddressFactory;
use Database\Factories\AgentAssignedApplianceFactory;
use Database\Factories\AgentBalanceHistoryFactory;
use Database\Factories\AgentCommissionFactory;
use Database\Factories\AgentFactory;
use Database\Factories\AgentReceiptFactory;
use Database\Factories\AgentSoldApplianceFactory;
use Database\Factories\AgentTransactionFactory;
use Database\Factories\AssetTypeFactory;
use Database\Factories\CityFactory;
use Database\Factories\ClusterFactory;
use Database\Factories\CompanyDatabaseFactory;
use Database\Factories\CompanyFactory;
use Database\Factories\ConnectionGroupFactory;
use Database\Factories\ConnectionTypeFactory;
use Database\Factories\ManufacturerFactory;
use Database\Factories\MeterFactory;
use Database\Factories\MeterParameterFactory;
use Database\Factories\MeterTariffFactory;
use Database\Factories\MeterTokenFactory;
use Database\Factories\MeterTypeFactory;
use Database\Factories\MiniGridFactory;
use Database\Factories\PaymentHistoryFactory;
use Database\Factories\PersonFactory;
use Database\Factories\SubConnectionTypeFactory;
use Database\Factories\SubTargetFactory;
use Database\Factories\TargetFactory;
use Database\Factories\TimeOfUsageFactory;
use Database\Factories\TransactionFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\RefreshMultipleDatabases;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\Collection;
use function Symfony\Component\Translation\t;

trait CreateEnvironments
{
    use RefreshMultipleDatabases, WithFaker;

    private $user, $company, $city, $cluster, $miniGrid, $connectionType, $manufacturer, $meterType, $meter, $meterParameter,
        $meterTariff, $person, $token, $transaction, $connectionGroup, $connectonType, $subConnectionType, $target,
        $subTarget, $agent, $agentCommission, $address, $timeOfUsage, $companyDatabase, $meterToken, $paymentHistory,
        $assetType, $assignedAppliance, $soldAppliance, $agentReceipt, $agentTransaction, $agentBalanceHistory;

    private $clusters = [], $miniGrids = [], $connectionGroups = [], $connectonTypes = [], $subConnectionTypes = [], $meterTypes = [],
        $manufacturers = [], $cities = [], $meterTariffs = [], $targets = [], $subTargets = [], $people = [], $assignedAppliances = [],
        $addresses = [], $agents = [], $agentCommissions = [], $transactions = [], $paymentHistories = [], $meters = [], $assetTypes = [],
        $soldAppliances = [], $agentReceipts = [], $agentTransactions = [], $agentBalanceHistories = [];

    protected function createTestData()
    {
        $this->user = UserFactory::new()->create();
        $this->company = CompanyFactory::new()->create();
        $this->companyDatabase = CompanyDatabaseFactory::new()->create();

    }

    protected function createCluster($clusterCount = 1)
    {
        while ($clusterCount > 0) {

            $cluster = ClusterFactory::new()->create([
                'name' => $this->faker->unique()->companySuffix,
                'manager_id' => $this->user->id,
            ]);
            $this->clusters[] = $cluster;
            $clusterCount--;
        }

        if (count($this->clusters) > 0) {
            $this->cluster = $this->clusters[0];
        }
    }

    protected function createMiniGrid($miniGridCount = 1)
    {
        while ($miniGridCount > 0) {
            $miniGrid = MiniGridFactory::new()->create([
                'cluster_id' => $this->getRandomIdFromList($this->clusters),
                'name' => $this->faker->unique()->companySuffix,
            ]);
            $this->miniGrids[] = $miniGrid;
            $miniGridCount--;
        }

        if (count($this->miniGrids) > 0) {
            $this->miniGrid = $this->miniGrids[0];
        }
    }

    protected function createCity($cityCount = 1)
    {
        while ($cityCount > 0) {
            $city = CityFactory::new()->create([
                'name' => $this->faker->unique()->citySuffix,
                'country_id' => 1,
                'mini_grid_id' => $this->getRandomIdFromList($this->miniGrids),
                'cluster_id' => $this->getRandomIdFromList($this->clusters),
            ]);
            $this->cities[] = $city;
            $cityCount--;
        }

        if (count($this->cities) > 0) {
            $this->city = $this->cities[0];
        }
    }

    protected function getMeter(): mixed
    {
        $this->createTestData();
        $meter = MeterFactory::new()->create([
            'meter_type_id' => $this->meterType->id,
            'in_use' => true,
            'manufacturer_id' => $this->manufacturer->id,
            'serial_number' => str_random(36),
        ]);

        $meterParameter = MeterParameterFactory::new()->create([
            'owner_type' => 'person',
            'owner_id' => $this->person->id,
            'meter_id' => $meter->id,
            'tariff_id' => $this->meterTariff->id,
            'connection_type_id' => $this->connectionType->id,
            'connection_group_id' => $this->connectionGroup->id,
        ]);
        return $meter;
    }

    protected function createMeterWithGeo(): void
    {
        $this->createTestData();
        $meterCunt = 2;
        while ($meterCunt > 0) {
            $meter = MeterFactory::new()->create([
                'meter_type_id' => $this->meterType->id,
                'in_use' => true,
                'manufacturer_id' => 1,
                'serial_number' => str_random(36),
            ]);
            $geographicalInformation = GeographicalInformation::query()->make(['points' => '111,222']);
            $this->person = PersonFactory::new()->create();
            $addressData = [
                'city_id' => $this->city->id,
                'geo_id' => $geographicalInformation->id,
            ];

            $meterParameter = MeterParameterFactory::new()->create([
                'owner_type' => 'person',
                'owner_id' => $this->person->id,
                'meter_id' => $meter->id,
                'tariff_id' => $this->meterTariff->id,
                'connection_type_id' => $this->connectionType->id,
                'connection_group_id' => $this->connectionGroup->id,
            ]);
            $address = Address::query()->make([
                'email' => isset($addressData['email']) ?: null,
                'phone' => isset($addressData['phone']) ?: null,
                'street' => isset($addressData['street']) ?: null,
                'city_id' => isset($addressData['city_id']) ?: null,
                'geo_id' => isset($addressData['geo_id']) ?: null,
                'is_primary' => isset($addressData['is_primary']) ?: 0,
            ]);
            $address->owner()->associate($meterParameter)->save();
            $geographicalInformation->owner()->associate($meterParameter)->save();
            $meterCunt--;
        }
    }

    protected function createMeterWithTransaction()
    {
        $meter = $this->getMeter();
        $this->transaction = TransactionFactory::new()->create([
            'id' => 1,
            'amount' => $this->faker->unique()->randomNumber(),
            'sender' => $this->faker->phoneNumber,
            'message' => $meter->serial_number,
            'original_transaction_id' => $this->faker->unique()->randomNumber(),
            'original_transaction_type' => 'airtel_transaction',
        ]);
        $this->token = MeterTokenFactory::new()->create([
            'meter_id' => $meter->id,
            'token' => $this->faker->unique()->randomNumber(),
        ]);
        $paymentHistory = PaymentHistoryFactory::new()->create([
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'payment_service' => 'airtel_transaction',
            'sender' => $this->faker->phoneNumber,
            'payment_type' => 'energy',
            'paid_for_type' => 'token',
            'paid_for_id' => $this->token->id,
            'payer_type' => 'person',
            'payer_id' => $this->person->id,
        ]);
        return $meter;
    }

    protected function createMetersWithDifferentMeterTypes($meterCountPerMeterType = 1): void
    {

        $meterTypeCount = count($this->meterTypes);

        while ($meterTypeCount > 0) {

            while ($meterCountPerMeterType > 0) {
                $meter = MeterFactory::new()->create([
                    'meter_type_id' => $this->meterType->id,
                    'in_use' => true,
                    'manufacturer_id' => $this->manufacturer->id,
                    'serial_number' => str_random(36),
                ]);

                $meterParameter = MeterParameterFactory::new()->create([
                    'owner_type' => 'person',
                    'owner_id' => $this->person->id,
                    'meter_id' => $meter->id,
                    'tariff_id' => $this->meterTariff->id,
                    'connection_type_id' => $this->connectionType->id,
                    'connection_group_id' => $this->connectionGroup->id,
                ]);
                $meterCountPerMeterType--;
            }
            $meterTypeCount--;
        }

    }

    protected function createMeterManufacturer($manufacturerCount = 1): void
    {
        while ($manufacturerCount > 0) {

            $manufacturer = ManufacturerFactory::new()->create();
            $address = Address::query()->make([
                'email' => $this->faker->email,
                'phone' => $this->faker->phoneNumber,
                'street' => $this->faker->streetAddress,
                'city_id' => 1,
            ]);
            $address->owner()->associate($manufacturer);
            $address->save();

            $this->manufacturers[] = $manufacturer;


            $manufacturerCount--;
        }
        if (count($this->manufacturers) > 0) {
            $this->manufacturer = $this->manufacturers[0];
        }

    }

    protected function createMeterTariff($meterTariffCount = 1, $withTimeOfUsage = false): void
    {
        while ($meterTariffCount > 0) {
            $meterTariff = MeterTariffFactory::new()->create();
            $this->meterTariffs[] = $meterTariff;

            if ($withTimeOfUsage) {
                $timeOfUsage = TimeOfUsageFactory::new()->create([
                    'tariff_id' => $meterTariff->id,
                    'start' => '00:00',
                    'end' => '01:00',
                    'value' => $this->faker->randomFloat(2, 0, 10),
                ]);
            }

            $meterTariffCount--;
        }
        if (count($this->meterTariffs) > 0) {
            $this->meterTariff = $this->meterTariffs[0];
        }

    }

    protected function createConnectionType($connectionTypeCount = 1, $subConnectionTypeCount = 1): void
    {
        while ($connectionTypeCount > 0) {
            $connectionType = ConnectionTypeFactory::new()->create();
            $this->connectonTypes[] = $connectionType;

            while ($subConnectionTypeCount > 0) {
                $subConnectionType =
                    SubConnectionTypeFactory::new()->create([
                        'connection_type_id' => $connectionType->id,
                        'tariff_id' => $this->meterTariff->id
                    ]);
                $this->subConnectionTypes[] = $subConnectionType;

                $subConnectionTypeCount--;
            }
            if (count($this->subConnectionTypes) > 0) {
                $this->subConnectionType = $this->subConnectionTypes[0];
            }
            $connectionTypeCount--;
        }
        if (count($this->connectonTypes) > 0) {
            $this->connectionType = $this->connectonTypes[0];
        }
    }

    protected function createConnectionGroup($connectionGroupCount = 1): void
    {
        while ($connectionGroupCount > 0) {
            $connectionGroup = ConnectionGroupFactory::new()->create();
            $this->connectionGroups[] = $connectionGroup;
            $connectionGroupCount--;
        }
        if (count($this->connectionGroups) > 0) {
            $this->connectionGroup = $this->connectionGroups[0];
        }

    }

    protected function createMeterType($meterTypeCount = 1): void
    {
        while ($meterTypeCount > 0) {
            $meterType = MeterTypeFactory::new()->create();
            $this->meterTypes[] = $meterType;

            $meterTypeCount--;
        }

        if (count($this->meterTypes) > 0) {
            $this->meterType = $this->meterTypes[0];
        }
    }

    protected function createMeter($meterCount = 1): void
    {

        while ($meterCount > 0) {
            $meter = MeterFactory::new()->create([
                'meter_type_id' => $this->meterType->id,
                'in_use' => true,
                'manufacturer_id' => $this->getRandomIdFromList($this->manufacturers),
                'serial_number' => str_random(36),
            ]);
            $geographicalInformation = GeographicalInformation::query()->make(['points' => '111,222']);
            $person = PersonFactory::new()->create();
            $addressData = [
                'city_id' => $this->getRandomIdFromList($this->cities),
                'geo_id' => $geographicalInformation->id,
            ];
            $meterParameter = MeterParameterFactory::new()->create([
                'owner_type' => 'person',
                'owner_id' => $person->id,
                'meter_id' => $meter->id,
                'tariff_id' => $this->getRandomIdFromList($this->meterTariffs),
                'connection_type_id' => $this->getRandomIdFromList($this->connectonTypes),
                'connection_group_id' => $this->getRandomIdFromList($this->connectionGroups),
            ]);
            $address = Address::query()->make([
                'email' => $addressData['email'] ?? null,
                'phone' => $addressData['phone'] ?? null,
                'street' => $addressData['street'] ?? null,
                'city_id' => $addressData['city_id'] ?? null,
                'geo_id' => $addressData['geo_id'] ?? null,
                'is_primary' => $addressData['is_primary'] ?? 0,
            ]);
            $address->owner()->associate($meterParameter)->save();
            $geographicalInformation->owner()->associate($meterParameter)->save();

            $meterCount--;
        }

    }

    protected function createTarget($targetCount = 1): void
    {
        while ($targetCount > 0) {
            $target = TargetFactory::new()->create();
            $this->targets[] = $target;
            $targetCount--;
        }
        if (count($this->targets) > 0) {
            $this->target = $this->targets[0];
        }
    }

    protected function createSubTarget($subTargetCount = 1): void
    {
        while ($subTargetCount > 0) {
            $subTarget = SubTargetFactory::new()->create([
                'target_id' => $this->getRandomIdFromList($this->targets),
                'connection_id' => $this->getRandomIdFromList($this->connectionGroups),
            ]);
            $this->subTargets[] = $subTarget;
            $subTargetCount--;
        }
        if (count($this->subTargets) > 0) {
            $this->subTarget = $this->subTargets[0];
        }
    }

    protected function createPerson($personCount = 1)
    {

        while ($personCount > 0) {
            $person = PersonFactory::new()->create();
            $this->people[] = $person;
            $personCount--;
        }
        if (count($this->people) > 0) {
            $this->person = $this->people[0];
        }

    }

    protected function createAgent($agentCount = 1)
    {
        $this->createPerson($agentCount);

        foreach ($this->people as $person) {
            $agent = AgentFactory::new()->create([
                'person_id' => $person->id,
                'name' => $person->name,
                'agent_commission_id' => $this->getRandomIdFromList($this->agentCommissions),
            ]);
            $this->agents[] = $agent;

            AddressFactory::new()->create([
                'owner_type' => 'person',
                'owner_id' => $person->id,
                'email' => $person->email,
                'phone' => $person->phone,
                'street' => $person->street,
                'city_id' => $person->city_id,
                'geo_id' => $person->geo_id,
                'is_primary' => 1,
            ]);
        }

        if (count($this->agents) > 0) {
            $this->agent = $this->agents[0];
        }
    }

    protected function createAgentCommission($agentCommissionCount = 1)
    {
        while ($agentCommissionCount > 0) {
            $agentCommission = AgentCommissionFactory::new()->create();
            $this->agentCommissions[] = $agentCommission;

            $agentCommissionCount--;
        }
        if (count($this->agentCommissions) > 0) {
            $this->agentCommission = $this->agentCommissions[0];
        }

    }

    protected function createAssetType($assetTypeCount = 1)
    {
        while ($assetTypeCount > 0) {
            $assetType = AssetTypeFactory::new()->create();
            $this->assetTypes[] = $assetType;

            $assetTypeCount--;
        }
        if (count($this->assetTypes) > 0) {
            $this->assetType = $this->assetTypes[0];
        }

    }

    protected function createAssignedAppliances($applianceCount = 1)
    {
        $this->createAssetType($applianceCount);

        while ($applianceCount > 0) {
            $assignedAppliance = AgentAssignedApplianceFactory::new()->create([
                'agent_id' => $this->getRandomIdFromList($this->agents),
                'appliance_type_id' => $this->getRandomIdFromList($this->assetTypes),
                'user_id' => $this->user->id,
                'cost' => $this->faker->randomFloat(2, 1, 100),
            ]);
            $this->assignedAppliances[] = $assignedAppliance;

            $applianceCount--;
        }
        if (count($this->assignedAppliances) > 0) {
            $this->assignedAppliance = $this->assignedAppliances[0];
        }
    }

    protected function createAgentSoldAppliance($soldApplianceCount = 1)
    {
        while ($soldApplianceCount > 0) {
            $soldAppliance = AgentSoldApplianceFactory::new()->create([
                'agent_assigned_appliance_id' => $this->getRandomIdFromList($this->assignedAppliances),
                'person_id' => $this->getRandomIdFromList($this->people),
            ]);
            $this->soldAppliances[] = $soldAppliance;

            $soldApplianceCount--;
        }
        if (count($this->soldAppliances) > 0) {
            $this->soldAppliance = $this->soldAppliances[0];
        }
    }


    protected function createAgentReceipt($agentReceiptCount = 1,$amount =50)
    {
        while ($agentReceiptCount > 0) {

            $agentReceipt = AgentReceiptFactory::new()->create([
                'user_id' => 1,
                'agent_id' => $this->getRandomIdFromList($this->agents),
                'amount' => $amount,
                'last_controlled_balance_history_id' => $this->getRandomIdFromList($this->agentBalanceHistories),
            ]);
            $this->agentReceipts[] = $agentReceipt;

            $agentReceiptCount--;
        }
        if (count($this->agentReceipts) > 0) {
            $this->agentReceipt = $this->agentReceipts[0];
        }
    }

    protected function createAgentBalanceHistory($agentBalanceHistoryCount = 1)
    {
        while ($agentBalanceHistoryCount > 0) {
            $agentBalanceHistory = AgentBalanceHistoryFactory::new()->create([
                'agent_id' => $this->getRandomIdFromList($this->agents),
                'amount' => $this->faker->randomFloat(2, 1, 100),
                'transaction_id' => $this->getRandomIdFromList($this->transactions),
                'available_balance' => 0,
                'due_to_supplier' => (-1 * $this->faker->randomFloat(2, 1, 100)),
                'trigger_id' => $this->getRandomIdFromList($this->transactions),
                'trigger_type' => 'agent_transaction'
            ]);
            $this->agentBalanceHistories[] = $agentBalanceHistory;

            $agentBalanceHistoryCount--;
        }
        if (count($this->agentBalanceHistories) > 0) {
            $this->agentBalanceHistory = $this->agentBalanceHistories[0];
        }
    }

    protected function createAgentTransaction($agentTransactionCount = 1, $amount = 100,$agentId = null)
    {
        while ($agentTransactionCount > 0) {

            $transaction = TransactionFactory::new()->make([
                'amount' =>  $amount,
                'sender' => $this->faker->phoneNumber,
                'message' => '47000268748',
            ]);

            if(!$agentId){
                $agentId = $this->getRandomIdFromList($this->agents);
            }

            $agent = collect($this->agents)->where('id', $agentId)->first();
            $agentTransaction = AgentTransactionFactory::new()->create([
                'agent_id' => $agent->id,
                'device_id' => '123456789',
                'status' => 1,
                'manufacturer_transaction_type' => 'test',
                'manufacturer_transaction_id' => 1,
            ]);
            $this->agentTransactions[] = $agentTransaction;

            $transaction->originalTransaction()->associate($agentTransaction);
            $transaction->save();

            $agentBalanceHistory = AgentBalanceHistoryFactory::new()->create([
                'agent_id' => $agent->id,
                'amount' => ($amount) > 0 ? (-1 * ($amount)) : ($amount),
                'transaction_id' => $agentTransaction->id,
                'available_balance' => $agent->balance,
                'due_to_supplier' => $agent->due_to_energy_supplier,
                'trigger_id' => $agentTransaction->id,
                'trigger_type' => 'agent_transaction'
            ]);
            $this->agentBalanceHistories[] = $agentBalanceHistory;

            $commission = $this->agentCommission;

            $agentBalanceHistory = AgentBalanceHistoryFactory::new()->create([
                'agent_id' => $this->getRandomIdFromList($this->agents),
                'amount' => ($amount * $commission->energy_commission) < 0 ?
                    (-1 * ($amount * $commission->energy_commission)) : ($amount * $commission->energy_commission),
                'transaction_id' => $agentTransaction->id,
                'available_balance' => $agent->balance,
                'due_to_supplier' => $agent->due_to_energy_supplier,
                'trigger_id' => $commission->id,
                'trigger_type' => 'agent_commission'
            ]);
            $this->agentBalanceHistories[] = $agentBalanceHistory;

            $agentTransactionCount--;
        }
        if (count($this->agentTransactions) > 0) {
            $this->agentTransaction = $this->agentTransactions[0];
        }
    }

    private function getRandomIdFromList(array $list, $startIndex = 1, $endIndex = null): int
    {

        $ids = collect($list)->pluck('id')->toArray();

        if ($endIndex === null) {
            $endIndex = count($ids);
        }

        return rand($startIndex, $endIndex);
    }
}