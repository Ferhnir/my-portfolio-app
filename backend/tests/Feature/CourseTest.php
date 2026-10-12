<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_courses(): void
    {
        $this->getJson('/api/courses')->assertUnauthorized();
    }

    public function test_store_appends_when_sort_order_is_omitted(): void
    {
        $user = User::factory()->create();
        Course::query()->create(['name' => 'First', 'sort_order' => 1]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/courses', [
                'name' => 'Second',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Second')
            ->assertJsonPath('data.sort_order', 2);

        $this->assertSame(
            [1, 2],
            Course::query()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_store_inserts_at_the_requested_position(): void
    {
        $user = User::factory()->create();
        Course::query()->create(['name' => 'First', 'sort_order' => 1]);
        Course::query()->create(['name' => 'Second', 'sort_order' => 2]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/courses', [
                'name' => 'Inserted',
                'sort_order' => 1,
                'start_date' => '2020-01-01',
                'end_date' => '2021-01-01',
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Inserted', 'First', 'Second'],
            Course::query()->orderBy('sort_order')->pluck('name')->all(),
        );
    }

    public function test_update_moves_a_row_and_shifts_the_others(): void
    {
        $user = User::factory()->create();
        Course::query()->create(['name' => 'First', 'sort_order' => 1]);
        Course::query()->create(['name' => 'Second', 'sort_order' => 2]);
        $third = Course::query()->create(['name' => 'Third', 'sort_order' => 3]);

        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/courses/'.$third->id, [
                'sort_order' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Third', 'First', 'Second'],
            Course::query()->orderBy('sort_order')->pluck('name')->all(),
        );
    }

    public function test_delete_closes_the_gap(): void
    {
        $user = User::factory()->create();
        Course::query()->create(['name' => 'First', 'sort_order' => 1]);
        $second = Course::query()->create(['name' => 'Second', 'sort_order' => 2]);
        Course::query()->create(['name' => 'Third', 'sort_order' => 3]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/courses/'.$second->id)
            ->assertNoContent();

        $this->assertSame(
            ['First', 'Third'],
            Course::query()->orderBy('sort_order')->pluck('name')->all(),
        );
        $this->assertSame(
            [1, 2],
            Course::query()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_store_rejects_an_end_date_before_the_start_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/courses', [
                'name' => 'Example',
                'start_date' => '2022-01-01',
                'end_date' => '2021-01-01',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    }
}
