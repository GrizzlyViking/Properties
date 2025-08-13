<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Corporation;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $corp = Corporation::create([
            'name' => 'Test Corporation',
        ]);

        // buildings
        $corp->buildings()->createMany(Building::factory()->count(3)->make()->toArray());
        $corp->refresh();
        /** @var Building $building */
        foreach ($corp->buildings as $building) {
            $building->properties()->createMany(Property::factory()->count(5)->make()->toArray());
            $building->refresh();
            /** @var Property $property */
            foreach ($building->properties as $property) {
                $tenancy_period = $property->tenancyPeriods()->create(['name' => 'Test Period', 'start_date' => now(), 'end_date' => now()->addYear()]);
                $tenancy_period->tenants()->createMany(Tenant::factory()->count(4)->make()->toArray());
            }
        }
    }
}
