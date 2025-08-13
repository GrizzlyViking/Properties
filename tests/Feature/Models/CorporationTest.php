<?php

namespace Tests\Feature\Models;

use App\Enums\Type;
use App\Models\Building;
use App\Models\Corporation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CorporationTest extends TestCase{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_corporation()
    {
        $corporation = Corporation::factory()->create([
            'name' => 'Test Corp'
        ]);

        $this->assertDatabaseHas('corporations', [
            'name' => 'Test Corp'
        ]);

        $this->assertEquals('Test Corp', $corporation->name);
    }

    #[Test]
    public function it_can_have_many_buildings()
    {
        $corporation = Corporation::factory()->create();
        Building::factory()->count(3)->create([
            'corporation_id' => $corporation->id
        ]);

        $this->assertCount(3, $corporation->buildings);
        $this->assertInstanceOf(Building::class, $corporation->buildings->first());
    }

    #[Test]
    public function it_cascades_deletes_to_buildings()
    {
        $corporation = Corporation::factory()->create();
        Building::factory()->count(2)->create([
            'corporation_id' => $corporation->id
        ]);

        $corporation->delete();

        $this->assertDatabaseCount('buildings', 0);
    }

    #[Test]
    public function it_has_correct_type()
    {
        $corporation = Corporation::factory()->create();

        $this->assertEquals(Type::CORPORATION->value, $corporation->getType());
    }

    #[Test]
    public function it_has_correct_height()
    {
        $corporation = Corporation::factory()->create();

        $this->assertEquals(0, $corporation->getHeight());
    }

    #[Test]
    public function it_can_update_corporation_details()
    {
        $corporation = Corporation::factory()->create([
            'name' => 'Old Name'
        ]);

        $corporation->update([
            'name' => 'New Name'
        ]);

        $this->assertDatabaseHas('corporations', [
            'id' => $corporation->id,
            'name' => 'New Name'
        ]);
    }

    #[Test]
    public function it_requires_a_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Corporation::factory()->create([
            'name' => null
        ]);
    }

    #[Test]
    public function it_can_fetch_all_buildings_for_a_corporation()
    {
        $corporation = Corporation::factory()->create();
        $buildings = Building::factory()->count(5)->create([
            'corporation_id' => $corporation->id
        ]);

        $fetchedBuildings = $corporation->buildings;

        $this->assertCount(5, $fetchedBuildings);
        $buildings->each(function ($building) use ($fetchedBuildings) {
            $this->assertTrue($fetchedBuildings->contains($building));
        });
    }
}

