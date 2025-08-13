<?php

namespace Tests\Feature\Models;

use App\Enums\Type;
use App\Models\Building;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\TenancyPeriod;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_property()
    {
        $property = Property::factory()->create([
            'name' => 'Test Unit',
            'monthly_rent' => 1500.50
        ]);

        $this->assertDatabaseHas('properties', [
            'name' => 'Test Unit',
            'monthly_rent' => 1500.50
        ]);
    }

    #[Test]
    public function it_belongs_to_a_building()
    {
        $building = Building::factory()->create();
        $property = Property::factory()->create([
            'building_id' => $building->id
        ]);

        $this->assertInstanceOf(Building::class, $property->building);
        $this->assertEquals($building->id, $property->building->id);
    }

    #[Test]
    public function it_has_correct_type()
    {
        $property = Property::factory()->create();

        $this->assertEquals(Type::PROPERTY->value, $property->getType());
    }

    #[Test]
    public function it_has_correct_height()
    {
        $property = Property::factory()->create();

        $this->assertEquals(Type::PROPERTY->height(), $property->getHeight());
    }

    #[Test]
    public function it_can_have_tenancy_periods()
    {
        $property = Property::factory()->create();
        $tenant = Tenant::factory()->create();

        TenancyPeriod::create([
            'name' => 'Test Period',
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31'
        ]);

        $property->refresh();

        $this->assertTrue($property->tenancyPeriods()->exists());
        $this->assertInstanceOf(TenancyPeriod::class, $property->tenancyPeriods->first());
    }

    #[Test]
    public function it_can_have_many_tenants()
    {
        $property = Property::factory()->create();
        $tenants = Tenant::factory()->count(2)->create();

        foreach ($tenants as $tenant) {
            TenancyPeriod::create([
                'name' => 'Test Period',
                'property_id' => $property->id,
                'tenant_id' => $tenant->id,
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31'
            ]);
        }

        $this->assertCount(2, $property->tenants);
        $this->assertInstanceOf(Tenant::class, $property->tenants->first());
    }

    #[Test]
    public function it_requires_a_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Property::factory()->create([
            'name' => null
        ]);
    }

    #[Test]
    public function it_requires_monthly_rent()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Property::factory()->create([
            'monthly_rent' => null
        ]);
    }

    #[Test]
    public function it_requires_a_building()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Property::factory()->create([
            'building_id' => null
        ]);
    }

    #[Test]
    public function it_can_update_property_details()
    {
        $property = Property::factory()->create([
            'name' => 'Old Unit Name',
            'monthly_rent' => 1000.00
        ]);

        $property->update([
            'name' => 'New Unit Name',
            'monthly_rent' => 1200.00
        ]);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'name' => 'New Unit Name',
            'monthly_rent' => 1200.00
        ]);
    }

    #[Test]
    public function it_deletes_tenancy_periods_when_property_is_deleted()
    {
        $property = Property::factory()->create();
        $tenant = Tenant::factory()->create();

        TenancyPeriod::create([
            'name' => 'Test Period',
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31'
        ]);

        $property->delete();

        $this->assertDatabaseCount('tenancy_periods', 0);
        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]); // Tenant should still exist
    }

    #[Test]
    public function it_can_get_current_tenants()
    {
        $property = Property::factory()->create();
        $currentTenant = Tenant::factory()->create();
        $pastTenant = Tenant::factory()->create();
        $futureTenant = Tenant::factory()->create();

        // Past tenancy
        TenancyPeriod::create([
            'name' => 'Past Period',
            'property_id' => $property->id,
            'tenant_id' => $pastTenant->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]);

        // Current tenancy
        TenancyPeriod::create([
            'name' => 'Current Period',
            'property_id' => $property->id,
            'tenant_id' => $currentTenant->id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31'
        ]);

        // Future tenancy
        TenancyPeriod::create([
            'name' => 'Future Period',
            'property_id' => $property->id,
            'tenant_id' => $futureTenant->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31'
        ]);

        $currentTenants = $property->tenants()
            ->wherePivot('start_date', '<=', Carbon::now())
            ->wherePivot('end_date', '>=', Carbon::now())
            ->get();

        $this->assertCount(1, $currentTenants);
        $this->assertEquals($currentTenant->id, $currentTenants->first()->id);
    }
}
