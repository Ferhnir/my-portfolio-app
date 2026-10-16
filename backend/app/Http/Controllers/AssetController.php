<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetStoreRequest;
use App\Http\Requests\AssetUpdateRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Models\CaseStudy;
use App\Models\CaseStudySection;
use App\Services\AssetService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Throwable;

class AssetController extends Controller
{
    public function __construct(private readonly AssetService $assetService) {}

    public function index(CaseStudy $caseStudy): AnonymousResourceCollection
    {
        $this->authorize('view', $caseStudy);

        return AssetResource::collection($caseStudy->assets()->get());
    }

    /**
     * @throws Throwable
     */
    public function store(AssetStoreRequest $request, CaseStudy $caseStudy): AssetResource
    {
        $this->authorize('update', $caseStudy);

        return new AssetResource($this->assetService->store(
            $caseStudy,
            $this->uploadedFile($request),
            $request->string('collection')->toString(),
            $request->exists('sort_order') ? $request->integer('sort_order') : null,
        ));
    }

    /**
     * @throws Throwable
     */
    public function update(AssetUpdateRequest $request, CaseStudy $caseStudy, Asset $asset): AssetResource
    {
        $this->authorize('update', $caseStudy);

        return new AssetResource($this->assetService->update($asset, $request->validated()));
    }

    /**
     * @throws Throwable
     */
    public function destroy(CaseStudy $caseStudy, Asset $asset): Response
    {
        $this->authorize('update', $caseStudy);

        $this->assetService->delete($asset);

        return response()->noContent();
    }

    public function sectionIndex(CaseStudy $caseStudy, CaseStudySection $section): AnonymousResourceCollection
    {
        $this->authorize('view', $caseStudy);

        return AssetResource::collection($section->assets()->get());
    }

    /**
     * @throws Throwable
     */
    public function sectionStore(AssetStoreRequest $request, CaseStudy $caseStudy, CaseStudySection $section): AssetResource
    {
        $this->authorize('update', $caseStudy);

        return new AssetResource($this->assetService->store(
            $section,
            $this->uploadedFile($request),
            $request->string('collection')->toString(),
            $request->exists('sort_order') ? $request->integer('sort_order') : null,
        ));
    }

    /**
     * @throws Throwable
     */
    public function sectionUpdate(
        AssetUpdateRequest $request,
        CaseStudy $caseStudy,
        CaseStudySection $section,
        Asset $asset,
    ): AssetResource {
        $this->authorize('update', $caseStudy);

        return new AssetResource($this->assetService->update($asset, $request->validated()));
    }

    /**
     * @throws Throwable
     */
    public function sectionDestroy(CaseStudy $caseStudy, CaseStudySection $section, Asset $asset): Response
    {
        $this->authorize('update', $caseStudy);

        $this->assetService->delete($asset);

        return response()->noContent();
    }

    private function uploadedFile(AssetStoreRequest $request): UploadedFile
    {
        $file = $request->file('file');

        if (! $file instanceof UploadedFile) {
            abort(422, 'An image file is required.');
        }

        return $file;
    }
}
