<?php

namespace Tests\Feature;

use App\Models\TechStackCategory;
use App\Models\TechStackGroupCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechStackCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_unassigned_returns_only_categories_without_a_group(): void
    {
        $user = User::factory()->create();
        $assigned = TechStackCategory::factory()->create(['name' => 'Assigned']);
        $unassigned = TechStackCategory::factory()->create(['name' => 'Unassigned']);
        TechStackGroupCategory::factory()->create(['category_id' => $assigned->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/tech-stack-categories?unassigned=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $unassigned->id);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/tech-stack-categories')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
