<?php

namespace Tests\Feature;

use App\Models\Technology;
use App\Models\TechStackPlacement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Tests\TestCase;

class TechnologyUnassignedTest extends TestCase
{
    use RefreshDatabase;

    public function test_unassigned_lists_only_technologies_without_a_placement(): void
    {
        $placed = Technology::factory()->create(['name' => 'Placed', 'slug' => 'placed']);
        $free = Technology::factory()->create(['name' => 'Free', 'slug' => 'free']);
        TechStackPlacement::factory()->create(['technology_id' => $placed->id]);

        $response = $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/technologies?unassigned=1');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('Free'));
        $this->assertFalse($names->contains('Placed'));
    }

    public function test_index_without_the_filter_returns_every_technology(): void
    {
        $placed = Technology::factory()->create(['name' => 'Placed', 'slug' => 'placed']);
        Technology::factory()->create(['name' => 'Free', 'slug' => 'free']);
        TechStackPlacement::factory()->create(['technology_id' => $placed->id]);

        $response = $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/technologies');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('Free'));
        $this->assertTrue($names->contains('Placed'));
    }

    public function test_unassigned_rejects_a_method_that_is_not_a_relation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Technology::query()->unassigned(Request::create('/'), 'delete');
    }
}
