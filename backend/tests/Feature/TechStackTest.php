<?php

namespace Tests\Feature;

use Database\Seeders\TechStackSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechStackTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_category_can_be_reused_by_more_than_one_group(): void
    {
        $this->seed(TechStackSeeder::class);

        $response = $this->getJson('/api/tech-stack');

        $response->assertOk();

        $groups = collect($response->json('data'));
        $backend = $groups->firstWhere('name', 'Backend Ecosystem');
        $frontend = $groups->firstWhere('name', 'Frontend Engineering');

        $backendLanguages = collect($backend['categories'])->first(
            fn (array $row) => $row['category']['slug'] === 'core-languages'
        );
        $frontendLanguages = collect($frontend['categories'])->first(
            fn (array $row) => $row['category']['slug'] === 'core-languages'
        );

        $this->assertSame($backendLanguages['category']['id'], $frontendLanguages['category']['id']);
        $this->assertNotSame($backendLanguages['id'], $frontendLanguages['id']);

        $backendNames = collect($backendLanguages['technologies'])->pluck('technology.name');
        $frontendNames = collect($frontendLanguages['technologies'])->pluck('technology.name');

        $this->assertTrue($backendNames->contains('PHP'));
        $this->assertFalse($frontendNames->contains('PHP'));
        $this->assertTrue($frontendNames->contains('JavaScript (ES6+)'));
    }
}
