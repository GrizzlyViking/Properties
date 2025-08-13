<?php

namespace Tests\Feature\Models;

use App\Enums\Type;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\TenancyPeriod;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_tenant()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'comments' => 'Test comment'
        ]);

        $this->assertDatabaseHas('tenants', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'comments' => 'Test comment'
        ]);
    }

    #[Test]
    public function it_has_correct_type()
    {
        $tenant = Tenant::factory()->create();

        $this->assertEquals(Type::TENANT->value, $tenant->getType());
    }

    #[Test]
    public function it_has_correct_height()
    {
        $tenant = Tenant::factory()->create();

        $this->assertEquals(Type::TENANT->height(), $tenant->getHeight());
    }

    #[Test]
    public function it_can_have_tenancy_periods()
    {
        $tenant = Tenant::factory()->create();
        $property = Property::factory()->create();

        TenancyPeriod::create([
            'name' => 'Test Period',
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31'
        ]);

        $this->assertTrue($tenant->tenancyPeriods()->exists());
        $this->assertInstanceOf(TenancyPeriod::class, $tenant->tenancyPeriods->first());
    }

    #[Test]
    public function it_can_have_multiple_properties()
    {
        $tenant = Tenant::factory()->create();
        $properties = Property::factory()->count(2)->create();

        foreach ($properties as $property) {
            TenancyPeriod::create([
                'name' => 'Test Period',
                'property_id' => $property->id,
                'tenant_id' => $tenant->id,
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31'
            ]);
        }

        $this->assertCount(2, $tenant->properties);
        $this->assertInstanceOf(Property::class, $tenant->properties->first());
    }

    #[Test]
    public function it_requires_a_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Tenant::factory()->create([
            'name' => null
        ]);
    }

    #[Test]
    public function it_requires_an_email()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Tenant::factory()->create([
            'email' => null
        ]);
    }

    #[Test]
    public function it_requires_unique_email()
    {
        Tenant::factory()->create([
            'email' => 'test@example.com'
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Tenant::factory()->create([
            'email' => 'test@example.com'
        ]);
    }

    #[Test]
    public function it_can_update_tenant_details()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com'
        ]);

        $tenant->update([
            'name' => 'New Name',
            'email' => 'new@example.com'
        ]);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'New Name',
            'email' => 'new@example.com'
        ]);
    }

    #[Test]
    public function it_can_be_soft_deleted()
    {
        $tenant = Tenant::factory()->create();

        $tenant->delete();

        $this->assertSoftDeleted('tenants', [
            'id' => $tenant->id
        ]);
    }

    #[Test]
    public function it_cascades_delete_to_tenancy_periods()
    {
        $tenant = Tenant::factory()->create();
        $property = Property::factory()->create();

        TenancyPeriod::create([
            'name' => 'Test Period',
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31'
        ]);

        $tenant->forceDelete();

        $this->assertDatabaseEmpty('tenancy_periods');
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
    }

    #[Test]
    public function it_can_get_current_properties()
    {
        $tenant = Tenant::factory()->create();
        $currentProperty = Property::factory()->create();
        $pastProperty = Property::factory()->create();
        $futureProperty = Property::factory()->create();

        // Past tenancy
        TenancyPeriod::create([
            'name' => 'Past Period',
            'property_id' => $pastProperty->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]);

        // Current tenancy
        TenancyPeriod::create([
            'name' => 'Current Period',
            'property_id' => $currentProperty->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31'
        ]);

        // Future tenancy
        TenancyPeriod::create([
            'name' => 'Future Period',
            'property_id' => $futureProperty->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31'
        ]);

        $currentProperties = $tenant->properties()
            ->wherePivot('start_date', '<=', Carbon::now())
            ->wherePivot('end_date', '>=', Carbon::now())
            ->get();

        $this->assertCount(1, $currentProperties);
        $this->assertEquals($currentProperty->id, $currentProperties->first()->id);
    }
}
