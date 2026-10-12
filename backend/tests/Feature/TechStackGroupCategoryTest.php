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
            ->assertCreated();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/tech-stack-groups/{$group->id}/tech-stack-categories/{$category->id}", [
                'sort_order' => 2,
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.category_id.0', 'The selected category is already attached to this group.');

        $link = TechStackGroupCategory::query()->sole();

        $this->assertSame($group->id, $link->group_id);
        $this->assertSame($category->id, $link->category_id);
        $this->assertSame(1, $link->sort_order);
    }

    public function test_a_category_can_move_into_another_group(): void
    {
        $user = User::factory()->create();
        $origin = TechStackGroup::factory()->create();
        $destination = TechStackGroup::factory()->create();
        $stays = TechStackCategory::factory()->create();
        $moving = TechStackCategory::factory()->create();
        $alreadyThere = TechStackCategory::factory()->create();

        TechStackGroupCategory::factory()->create([
            'group_id' => $origin->id,
            'category_id' => $stays->id,
            'sort_order' => 1,
        ]);
        $link = TechStackGroupCategory::factory()->create([
            'group_id' => $origin->id,
            'category_id' => $moving->id,
            'sort_order' => 2,
        ]);
        TechStackGroupCategory::factory()->create([
            'group_id' => $destination->id,
            'category_id' => $alreadyThere->id,
            'sort_order' => 1,
        ]);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/tech-stack-group-category/{$link->id}", [
                'group_id' => $destination->id,
                'sort_order' => 1,
            ])
            ->assertOk();

        $this->assertSame($destination->id, $link->refresh()->group_id);
        $this->assertSame(1, $link->sort_order);
        $this->assertSame(1, TechStackGroupCategory::query()->where('group_id', $origin->id)->where('category_id', $stays->id)->value('sort_order'));
        $this->assertSame(2, TechStackGroupCategory::query()->where('group_id', $destination->id)->where('category_id', $alreadyThere->id)->value('sort_order'));
    }

    public function test_reordering_inside_a_group_shifts_the_other_rows(): void
    {
        $user = User::factory()->create();
        $group = TechStackGroup::factory()->create();
        $first = TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'sort_order' => 1,
        ]);
        $middle = TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'sort_order' => 2,
        ]);
        $last = TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'sort_order' => 3,
        ]);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/tech-stack-group-category/{$last->id}", [
                'sort_order' => 1,
            ])
            ->assertOk();

        $this->assertSame(1, $last->refresh()->sort_order);
        $this->assertSame(2, $first->refresh()->sort_order);
        $this->assertSame(3, $middle->refresh()->sort_order);
    }

    public function test_removing_a_category_closes_the_sort_gap(): void
    {
        $user = User::factory()->create();
        $group = TechStackGroup::factory()->create();
        $first = TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'sort_order' => 1,
        ]);
        $middle = TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'sort_order' => 2,
        ]);
        $last = TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'sort_order' => 3,
        ]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/tech-stack-group-category/{$middle->id}")
            ->assertNoContent();

        $this->assertModelMissing($middle);
        $this->assertSame(1, $first->refresh()->sort_order);
        $this->assertSame(2, $last->refresh()->sort_order);
    }

    public function test_siblings_are_the_other_categories_in_the_same_group(): void
    {
        $group = TechStackGroup::factory()->create();
        $otherGroup = TechStackGroup::factory()->create();
        $first = TechStackCategory::factory()->create();
        $middle = TechStackCategory::factory()->create();
        $last = TechStackCategory::factory()->create();
        $elsewhere = TechStackCategory::factory()->create();

        TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'category_id' => $first->id,
            'sort_order' => 1,
        ]);
        TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'category_id' => $middle->id,
            'sort_order' => 2,
        ]);
        TechStackGroupCategory::factory()->create([
            'group_id' => $group->id,
            'category_id' => $last->id,
            'sort_order' => 3,
        ]);
        TechStackGroupCategory::factory()->create([
            'group_id' => $otherGroup->id,
            'category_id' => $elsewhere->id,
            'sort_order' => 1,
        ]);

        $this->assertSame(
            [$first->id, $last->id],
            $middle->siblings($group)->get()->pluck('id')->all(),
        );
    }
}
