<?php

namespace Tests\Feature\Models;

use App\Enums\Type;
use App\Models\Building;
use App\Models\Corporation;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BuildingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_building()
    {
        Building::factory()->create([
            'name' => 'Test Building',
            'address' => '123 Test St',
            'city' => 'Test City',
            'zip_code' => '12345'
        ]);

        $this->assertDatabaseHas('buildings', [
            'name' => 'Test Building',
            'address' => '123 Test St',
            'city' => 'Test City',
            'zip_code' => '12345'
        ]);
    }

    #[Test]
    public function it_belongs_to_a_corporation()
    {
        $corporation = Corporation::factory()->create();
        $building = Building::factory()->create([
            'corporation_id' => $corporation->id
        ]);

        $this->assertInstanceOf(Corporation::class, $building->corporation);
        $this->assertEquals($corporation->id, $building->corporation->id);
    }

    #[Test]
    public function it_can_have_many_properties()
    {
        $building = Building::factory()->create();
        Property::factory()->count(3)->create([
            'building_id' => $building->id
        ]);

        $this->assertCount(3, $building->properties);
        $this->assertInstanceOf(Property::class, $building->properties->first());
    }

    #[Test]
    public function it_has_correct_type()
    {
        $building = Building::factory()->create();

        $this->assertEquals(Type::BUILDING->value, $building->getType());
    }

    #[Test]
    public function it_has_correct_height()
    {
        $building = Building::factory()->create();

        $this->assertEquals(Type::BUILDING->height(), $building->getHeight());
    }

    #[Test]
    public function it_requires_a_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Building::factory()->create([
            'name' => null
        ]);
    }

    #[Test]
    public function it_requires_a_zip_code()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Building::factory()->create([
            'zip_code' => null
        ]);
    }

    #[Test]
    public function it_requires_a_corporation()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Building::factory()->create([
            'corporation_id' => null
        ]);
    }

    #[Test]
    public function it_can_update_building_details()
    {
        $building = Building::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address'
        ]);

        $building->update([
            'name' => 'New Name',
            'address' => 'New Address'
        ]);

        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
            'name' => 'New Name',
            'address' => 'New Address'
        ]);
    }

    #[Test]
    public function it_can_have_nullable_address_and_city()
    {
        $building = Building::factory()->create([
            'address' => null,
            'city' => null
        ]);

        $this->assertNull($building->address);
        $this->assertNull($building->city);
        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
            'address' => null,
            'city' => null
        ]);
    }

    #[Test]
    public function it_cascades_deletes_to_properties()
    {
        $building = Building::factory()->create();
        Property::factory()->count(3)->create([
            'building_id' => $building->id
        ]);

        $building->delete();

        $this->assertDatabaseCount('properties', 0);
    }
}
