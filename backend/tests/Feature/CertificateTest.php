<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_certificates(): void
    {
        $this->getJson('/api/certificates')->assertOk();
    }

    public function test_guest_cannot_create_a_certificate(): void
    {
        $this->postJson('/api/certificates', [
            'name' => 'Example',
        ])->assertUnauthorized();
    }

    public function test_store_appends_when_sort_order_is_omitted(): void
    {
        $user = User::factory()->create();
        Certificate::query()->create(['name' => 'First', 'sort_order' => 1]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/certificates', [
                'name' => 'Second',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Second')
            ->assertJsonPath('data.sort_order', 2);

        $this->assertSame(
            [1, 2],
            Certificate::query()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_store_inserts_at_the_requested_position(): void
    {
        $user = User::factory()->create();
        Certificate::query()->create(['name' => 'First', 'sort_order' => 1]);
        Certificate::query()->create(['name' => 'Second', 'sort_order' => 2]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/certificates', [
                'name' => 'Inserted',
                'sort_order' => 1,
                'issued_at' => '2020-01-01',
                'expires_at' => '2021-01-01',
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Inserted', 'First', 'Second'],
            Certificate::query()->orderBy('sort_order')->pluck('name')->all(),
        );
    }

    public function test_update_moves_a_row_and_shifts_the_others(): void
    {
        $user = User::factory()->create();
        Certificate::query()->create(['name' => 'First', 'sort_order' => 1]);
        Certificate::query()->create(['name' => 'Second', 'sort_order' => 2]);
        $third = Certificate::query()->create(['name' => 'Third', 'sort_order' => 3]);

        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/certificates/'.$third->id, [
                'sort_order' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Third', 'First', 'Second'],
            Certificate::query()->orderBy('sort_order')->pluck('name')->all(),
        );
    }

    public function test_delete_closes_the_gap(): void
    {
        $user = User::factory()->create();
        Certificate::query()->create(['name' => 'First', 'sort_order' => 1]);
        $second = Certificate::query()->create(['name' => 'Second', 'sort_order' => 2]);
        Certificate::query()->create(['name' => 'Third', 'sort_order' => 3]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/certificates/'.$second->id)
            ->assertNoContent();

        $this->assertSame(
            ['First', 'Third'],
            Certificate::query()->orderBy('sort_order')->pluck('name')->all(),
        );
        $this->assertSame(
            [1, 2],
            Certificate::query()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_store_rejects_an_expiry_before_the_issue_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/certificates', [
                'name' => 'Example',
                'issued_at' => '2022-01-01',
                'expires_at' => '2021-01-01',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['expires_at']);
    }
}
