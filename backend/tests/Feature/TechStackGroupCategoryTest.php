<?php

namespace Tests\Feature;

use App\Models\TechStackCategory;
use App\Models\TechStackGroup;
use App\Models\TechStackGroupCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechStackGroupCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_attaching_the_same_category_again_keeps_the_original_row(): void
    {
        $user = User::factory()->create();
        $group = TechStackGroup::factory()->create();
        $category = TechStackCategory::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/tech-stack-groups/{$group->id}/tech-stack-categories/{$category->id}", [
                'sort_order' => 1,
            ])
            ->assertOk();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/tech-stack-groups/{$group->id}/tech-stack-categories/{$category->id}", [
                'sort_order' => 2,
            ])
            ->assertOk();

        $link = TechStackGroupCategory::query()->sole();

        $this->assertSame($group->id, $link->group_id);
        $this->assertSame($category->id, $link->category_id);
        $this->assertSame(1, $link->sort_order);
    }
}
