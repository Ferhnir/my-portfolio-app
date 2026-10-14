<?php

namespace Tests\Feature;

use App\Models\CaseStudy;
use App\Models\CaseStudyBlock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaseStudyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_lists_published_case_studies_only(): void
    {
        $this->caseStudy('Published', CaseStudy::STATUS_PUBLISHED);
        $this->caseStudy('Draft', CaseStudy::STATUS_DRAFT);

        $this->getJson('/api/case-studies')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Published');
    }

    public function test_authenticated_user_lists_drafts(): void
    {
        $user = User::factory()->create();
        $this->caseStudy('Published', CaseStudy::STATUS_PUBLISHED);
        $this->caseStudy('Draft', CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/case-studies')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_guest_cannot_create_a_case_study(): void
    {
        $this->postJson('/api/case-studies', [
            'title' => 'Hidden',
            'slug' => 'hidden',
        ])->assertUnauthorized();
    }

    public function test_guest_can_read_a_published_case_with_its_sections(): void
    {
        $caseStudy = $this->caseStudy('Published', CaseStudy::STATUS_PUBLISHED);
        $section = $caseStudy->sections()->create([
            'title' => 'The Challenge',
            'introduction' => 'One system was required.',
        ]);
        $block = $section->blocks()->create(['layout' => CaseStudyBlock::LAYOUT_CARD]);
        $block->points()->create(['body' => 'Tools were fragmented.']);

        $this->getJson('/api/case-studies/'.$caseStudy->id)
            ->assertOk()
            ->assertJsonPath('data.sections.0.title', 'The Challenge')
            ->assertJsonPath('data.sections.0.blocks.0.layout', 'card')
            ->assertJsonPath('data.sections.0.blocks.0.points.0.body', 'Tools were fragmented.');
    }

    public function test_guest_cannot_read_a_draft(): void
    {
        $caseStudy = $this->caseStudy('Draft', CaseStudy::STATUS_DRAFT);

        $this->getJson('/api/case-studies/'.$caseStudy->id)
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_a_bearer_token_can_read_a_draft(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $caseStudy = $this->caseStudy('Draft', CaseStudy::STATUS_DRAFT);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/case-studies')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Draft']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/case-studies/'.$caseStudy->id)
            ->assertOk()
            ->assertJsonPath('data.title', 'Draft');
    }

    public function test_store_appends_when_sort_order_is_omitted(): void
    {
        $user = User::factory()->create();
        $this->caseStudy('First', CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/case-studies', [
                'title' => 'Second',
                'slug' => 'second',
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 2)
            ->assertJsonPath('data.status', CaseStudy::STATUS_DRAFT);
    }

    public function test_store_inserts_at_the_requested_position(): void
    {
        $user = User::factory()->create();
        $this->caseStudy('First', CaseStudy::STATUS_DRAFT);
        $this->caseStudy('Second', CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/case-studies', [
                'title' => 'Inserted',
                'slug' => 'inserted',
                'sort_order' => 1,
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Inserted', 'First', 'Second'],
            CaseStudy::query()->orderBy('sort_order')->pluck('title')->all(),
        );
    }

    public function test_update_moves_a_case_study(): void
    {
        $user = User::factory()->create();
        $this->caseStudy('First', CaseStudy::STATUS_DRAFT);
        $this->caseStudy('Second', CaseStudy::STATUS_DRAFT);
        $third = $this->caseStudy('Third', CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/case-studies/'.$third->id, [
                'sort_order' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Third', 'First', 'Second'],
            CaseStudy::query()->orderBy('sort_order')->pluck('title')->all(),
        );
    }

    public function test_delete_closes_the_gap(): void
    {
        $user = User::factory()->create();
        $this->caseStudy('First', CaseStudy::STATUS_DRAFT);
        $second = $this->caseStudy('Second', CaseStudy::STATUS_DRAFT);
        $this->caseStudy('Third', CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/case-studies/'.$second->id)
            ->assertNoContent();

        $this->assertSame(
            ['First', 'Third'],
            CaseStudy::query()->orderBy('sort_order')->pluck('title')->all(),
        );
        $this->assertSame(
            [1, 2],
            CaseStudy::query()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_section_order_stays_inside_its_case_study(): void
    {
        $user = User::factory()->create();
        $firstCase = $this->caseStudy('First case', CaseStudy::STATUS_PUBLISHED);
        $secondCase = $this->caseStudy('Second case', CaseStudy::STATUS_PUBLISHED);
        $firstCase->sections()->create(['title' => 'Existing', 'sort_order' => 1]);
        $secondCase->sections()->create(['title' => 'Other', 'sort_order' => 1]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/case-studies/'.$firstCase->id.'/sections', [
                'title' => 'Inserted',
                'sort_order' => 1,
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(
            ['Inserted', 'Existing'],
            $firstCase->sections()->orderBy('sort_order')->pluck('title')->all(),
        );
        $this->assertSame(
            [1],
            $secondCase->sections()->orderBy('sort_order')->pluck('sort_order')->all(),
        );
    }

    public function test_a_section_from_another_case_is_not_found(): void
    {
        $firstCase = $this->caseStudy('First case', CaseStudy::STATUS_PUBLISHED);
        $secondCase = $this->caseStudy('Second case', CaseStudy::STATUS_PUBLISHED);
        $section = $firstCase->sections()->create(['title' => 'The Challenge']);

        $this->getJson('/api/case-studies/'.$secondCase->id.'/sections/'.$section->id)
            ->assertNotFound();
    }

    public function test_a_card_accepts_one_point_and_a_group_accepts_many(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy('Published', CaseStudy::STATUS_PUBLISHED);
        $section = $caseStudy->sections()->create(['title' => 'The Challenge']);

        $cardId = $this->actingAs($user, 'sanctum')
            ->postJson('/api/case-studies/'.$caseStudy->id.'/sections/'.$section->id.'/blocks', [
                'layout' => CaseStudyBlock::LAYOUT_CARD,
            ])
            ->assertCreated()
            ->json('data.id');

        $this->actingAs($user, 'sanctum')
            ->postJson($this->pointsUrl($caseStudy->id, $section->id, $cardId), [
                'body' => 'First point',
            ])
            ->assertCreated();

        $this->actingAs($user, 'sanctum')
            ->postJson($this->pointsUrl($caseStudy->id, $section->id, $cardId), [
                'body' => 'Second point',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);

        $groupId = $this->actingAs($user, 'sanctum')
            ->postJson('/api/case-studies/'.$caseStudy->id.'/sections/'.$section->id.'/blocks', [
                'layout' => CaseStudyBlock::LAYOUT_GROUP,
            ])
            ->assertCreated()
            ->json('data.id');

        $this->actingAs($user, 'sanctum')
            ->postJson($this->pointsUrl($caseStudy->id, $section->id, $groupId), [
                'body' => 'Grouped one',
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->actingAs($user, 'sanctum')
            ->postJson($this->pointsUrl($caseStudy->id, $section->id, $groupId), [
                'body' => 'Grouped two',
            ])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 2);
    }

    public function test_store_rejects_a_duplicate_slug(): void
    {
        $user = User::factory()->create();
        $this->caseStudy('First', CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/case-studies', [
                'title' => 'Again',
                'slug' => 'first',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    private function caseStudy(string $title, string $status): CaseStudy
    {
        return CaseStudy::query()->create([
            'title' => $title,
            'slug' => str($title)->slug()->toString(),
            'status' => $status,
        ]);
    }

    private function pointsUrl(int $caseStudyId, int $sectionId, int $blockId): string
    {
        return '/api/case-studies/'.$caseStudyId.'/sections/'.$sectionId.'/blocks/'.$blockId.'/points';
    }
}
