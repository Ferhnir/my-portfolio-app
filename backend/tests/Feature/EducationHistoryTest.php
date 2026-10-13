<?php

namespace Tests\Feature;

use App\Models\EducationHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_education_history(): void
    {
        $this->getJson('/api/education-histories')->assertOk();
    }

    public function test_guest_cannot_create_education_history(): void
    {
        $this->postJson('/api/education-histories', [
            'school_name' => 'Example',
        ])->assertUnauthorized();
    }

    public function test_store_appends_when_sort_order_is_omitted(): void
    {
        $user = User::factory()->create();
        EducationHistory::query()->create(['school_name' => 'First', 'sort_order' => 1]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/education-histories', [
                'school_name' => 'Second',
            ])
            ->assertCreated()
            ->assertJsonPath('data.school_name', 'Second')
            ->assertJsonPath('data.sort_order', 2);

        $this->assertSame(
            [1, 2],
            EducationHistory::query()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_store_inserts_at_the_requested_position(): void
    {
        $user = User::factory()->create();
        EducationHistory::query()->create(['school_name' => 'First', 'sort_order' => 1]);
        EducationHistory::query()->create(['school_name' => 'Second', 'sort_order' => 2]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/education-histories', [
                'school_name' => 'Inserted',
                'sort_order' => 1,
                'start_date' => '2020-01-01',
                'end_date' => '2021-01-01',
                'is_current' => false,
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Inserted', 'First', 'Second'],
            EducationHistory::query()->orderBy('sort_order')->pluck('school_name')->all(),
        );
    }

    public function test_update_moves_a_row_and_shifts_the_others(): void
    {
        $user = User::factory()->create();
        EducationHistory::query()->create(['school_name' => 'First', 'sort_order' => 1]);
        EducationHistory::query()->create(['school_name' => 'Second', 'sort_order' => 2]);
        $third = EducationHistory::query()->create(['school_name' => 'Third', 'sort_order' => 3]);

        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/education-histories/'.$third->id, [
                'sort_order' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Third', 'First', 'Second'],
            EducationHistory::query()->orderBy('sort_order')->pluck('school_name')->all(),
        );
    }

    public function test_delete_closes_the_gap(): void
    {
        $user = User::factory()->create();
        EducationHistory::query()->create(['school_name' => 'First', 'sort_order' => 1]);
        $second = EducationHistory::query()->create(['school_name' => 'Second', 'sort_order' => 2]);
        EducationHistory::query()->create(['school_name' => 'Third', 'sort_order' => 3]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/education-histories/'.$second->id)
            ->assertNoContent();

        $this->assertSame(
            ['First', 'Third'],
            EducationHistory::query()->orderBy('sort_order')->pluck('school_name')->all(),
        );
        $this->assertSame(
            [1, 2],
            EducationHistory::query()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_store_rejects_an_end_date_before_the_start_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/education-histories', [
                'school_name' => 'Example',
                'start_date' => '2022-01-01',
                'end_date' => '2021-01-01',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    }
}
