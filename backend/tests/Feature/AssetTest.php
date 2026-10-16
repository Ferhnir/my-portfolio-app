<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\CaseStudy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_guest_cannot_upload_an_image(): void
    {
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_PUBLISHED);

        $this->post($this->assetsUrl($caseStudy), [
            'file' => UploadedFile::fake()->image('cover.jpg'),
            'collection' => Asset::COLLECTION_COVER,
        ], ['Accept' => 'application/json'])->assertUnauthorized();
    }

    public function test_upload_stores_the_morph_alias_and_the_file(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);

        $response = $this->actingAs($user, 'sanctum')
            ->post($this->assetsUrl($caseStudy), [
                'file' => UploadedFile::fake()->image('cover.jpg'),
                'collection' => Asset::COLLECTION_COVER,
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.assetable_type', 'case_study')
            ->assertJsonPath('data.assetable_id', $caseStudy->id)
            ->assertJsonPath('data.collection', Asset::COLLECTION_COVER)
            ->assertJsonPath('data.sort_order', 1)
            ->assertJsonPath('data.original_name', 'cover.jpg');

        $asset = Asset::query()->findOrFail($response->json('data.id'));

        Storage::disk('public')->assertExists($asset->path);
        Storage::disk('public')->assertExists($asset->display_path);
        Storage::disk('public')->assertExists($asset->thumb_path);
        $response
            ->assertJsonPath('data.url', Storage::disk('public')->url($asset->display_path))
            ->assertJsonPath('data.thumb_url', Storage::disk('public')->url($asset->thumb_path));
        $this->assertDatabaseHas('assets', [
            'id' => $response->json('data.id'),
            'assetable_type' => 'case_study',
            'disk' => 'public',
        ]);
    }

    public function test_guest_can_list_images_on_a_published_case_study(): void
    {
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_PUBLISHED);
        $caseStudy->assets()->create([
            'collection' => Asset::COLLECTION_COVER,
            'disk' => 'public',
            'path' => 'assets/case_study/1/cover/cover.jpg',
            'original_name' => 'cover.jpg',
        ]);

        $this->getJson($this->assetsUrl($caseStudy))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.collection', Asset::COLLECTION_COVER)
            ->assertJsonPath('data.0.url', Storage::disk('public')->url('assets/case_study/1/cover/cover.jpg'));
    }

    public function test_guest_cannot_list_images_on_a_draft(): void
    {
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);

        $this->getJson($this->assetsUrl($caseStudy))->assertNotFound();
    }

    public function test_a_second_cover_replaces_the_first_file(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);

        $firstId = $this->actingAs($user, 'sanctum')
            ->post($this->assetsUrl($caseStudy), [
                'file' => UploadedFile::fake()->image('first.jpg'),
                'collection' => Asset::COLLECTION_COVER,
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->json('data.id');

        $first = Asset::query()->findOrFail($firstId);
        $firstFiles = [$first->path, $first->display_path, $first->thumb_path];

        $this->actingAs($user, 'sanctum')
            ->post($this->assetsUrl($caseStudy), [
                'file' => UploadedFile::fake()->image('second.jpg'),
                'collection' => Asset::COLLECTION_COVER,
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.original_name', 'second.jpg')
            ->assertJsonPath('data.sort_order', 1);

        $this->assertSame(1, $caseStudy->assets()->count());

        foreach ($firstFiles as $path) {
            Storage::disk('public')->assertMissing($path);
        }

        $second = $caseStudy->assets()->first();
        Storage::disk('public')->assertExists($second->path);
        Storage::disk('public')->assertExists($second->display_path);
        Storage::disk('public')->assertExists($second->thumb_path);
    }

    public function test_gallery_images_can_be_inserted_and_reordered(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->post($this->assetsUrl($caseStudy), [
                'file' => UploadedFile::fake()->image('one.jpg'),
                'collection' => Asset::COLLECTION_GALLERY,
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $secondId = $this->actingAs($user, 'sanctum')
            ->post($this->assetsUrl($caseStudy), [
                'file' => UploadedFile::fake()->image('two.jpg'),
                'collection' => Asset::COLLECTION_GALLERY,
                'sort_order' => 1,
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1)
            ->json('data.id');

        $this->assertSame(
            ['two.jpg', 'one.jpg'],
            $caseStudy->assets()->pluck('original_name')->all(),
        );

        $this->actingAs($user, 'sanctum')
            ->patchJson($this->assetsUrl($caseStudy).'/'.$secondId, [
                'sort_order' => 2,
            ])
            ->assertOk()
            ->assertJsonPath('data.sort_order', 2);

        $this->assertSame(
            ['one.jpg', 'two.jpg'],
            $caseStudy->assets()->orderBy('sort_order')->pluck('original_name')->all(),
        );
    }

    public function test_section_images_stay_on_the_section(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_PUBLISHED);
        $section = $caseStudy->sections()->create(['title' => 'The Challenge']);

        $this->actingAs($user, 'sanctum')
            ->post($this->sectionAssetsUrl($caseStudy, $section->id), [
                'file' => UploadedFile::fake()->image('section.jpg'),
                'collection' => Asset::COLLECTION_GALLERY,
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.assetable_type', 'case_study_section')
            ->assertJsonPath('data.assetable_id', $section->id);

        $this->getJson($this->assetsUrl($caseStudy))
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->getJson('/api/case-studies/'.$caseStudy->id)
            ->assertOk()
            ->assertJsonCount(0, 'data.assets')
            ->assertJsonPath('data.sections.0.assets.0.original_name', 'section.jpg');
    }

    public function test_an_image_from_another_case_study_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);
        $other = $this->caseStudy(CaseStudy::STATUS_DRAFT);
        $asset = $other->assets()->create([
            'collection' => Asset::COLLECTION_LOGO,
            'disk' => 'public',
            'path' => 'assets/case_study/other/logo/logo.jpg',
            'original_name' => 'logo.jpg',
        ]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson($this->assetsUrl($caseStudy).'/'.$asset->id)
            ->assertNotFound();

        $this->assertNotSoftDeleted($asset);
    }

    public function test_delete_removes_the_file(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);
        $path = 'assets/case_study/'.$caseStudy->id.'/gallery/one.jpg';
        $display = 'assets/case_study/'.$caseStudy->id.'/gallery/one-display.webp';
        $thumb = 'assets/case_study/'.$caseStudy->id.'/gallery/one-thumb.webp';
        Storage::disk('public')->put($path, 'image');
        Storage::disk('public')->put($display, 'display');
        Storage::disk('public')->put($thumb, 'thumb');
        $asset = $caseStudy->assets()->create([
            'collection' => Asset::COLLECTION_GALLERY,
            'disk' => 'public',
            'archive_disk' => 'public',
            'path' => $path,
            'display_path' => $display,
            'thumb_path' => $thumb,
            'original_name' => 'one.jpg',
        ]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson($this->assetsUrl($caseStudy).'/'.$asset->id)
            ->assertNoContent();

        $this->assertSoftDeleted($asset);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertMissing($display);
        Storage::disk('public')->assertMissing($thumb);
    }

    public function test_upload_rejects_a_file_that_is_not_an_image(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);

        $this->actingAs($user, 'sanctum')
            ->post($this->assetsUrl($caseStudy), [
                'file' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
                'collection' => Asset::COLLECTION_GALLERY,
            ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);
    }

    public function test_deleting_a_section_soft_deletes_only_its_images(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);
        $section = $caseStudy->sections()->create(['title' => 'The Challenge']);
        $cover = $caseStudy->assets()->create([
            'collection' => Asset::COLLECTION_COVER,
            'disk' => 'public',
            'path' => 'assets/case_study/cover.jpg',
            'original_name' => 'cover.jpg',
        ]);
        $sectionImage = $section->assets()->create([
            'collection' => Asset::COLLECTION_GALLERY,
            'disk' => 'public',
            'path' => 'assets/case_study_section/gallery.jpg',
            'original_name' => 'gallery.jpg',
        ]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/case-studies/'.$caseStudy->id.'/sections/'.$section->id)
            ->assertNoContent();

        $this->assertNotSoftDeleted($cover);
        $this->assertSoftDeleted($sectionImage);
    }

    public function test_deleting_a_case_study_soft_deletes_its_images(): void
    {
        $user = User::factory()->create();
        $caseStudy = $this->caseStudy(CaseStudy::STATUS_DRAFT);
        $section = $caseStudy->sections()->create(['title' => 'The Result']);
        $cover = $caseStudy->assets()->create([
            'collection' => Asset::COLLECTION_COVER,
            'disk' => 'public',
            'path' => 'assets/case_study/cover.jpg',
            'original_name' => 'cover.jpg',
        ]);
        $sectionImage = $section->assets()->create([
            'collection' => Asset::COLLECTION_GALLERY,
            'disk' => 'public',
            'path' => 'assets/case_study_section/gallery.jpg',
            'original_name' => 'gallery.jpg',
        ]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/case-studies/'.$caseStudy->id)
            ->assertNoContent();

        $this->assertSoftDeleted($cover);
        $this->assertSoftDeleted($sectionImage);
    }

    private function caseStudy(string $status): CaseStudy
    {
        return CaseStudy::query()->create([
            'title' => 'Example '.$status,
            'slug' => 'example-'.$status.'-'.fake()->unique()->numerify('###'),
            'status' => $status,
        ]);
    }

    private function assetsUrl(CaseStudy $caseStudy): string
    {
        return '/api/case-studies/'.$caseStudy->id.'/assets';
    }

    private function sectionAssetsUrl(CaseStudy $caseStudy, int $sectionId): string
    {
        return '/api/case-studies/'.$caseStudy->id.'/sections/'.$sectionId.'/assets';
    }
}
