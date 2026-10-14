<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseStudyBlockStoreRequest;
use App\Http\Requests\CaseStudyBlockUpdateRequest;
use App\Http\Resources\CaseStudyBlockResource;
use App\Models\CaseStudy;
use App\Models\CaseStudyBlock;
use App\Models\CaseStudySection;
use App\Services\CaseStudyBlockService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class CaseStudyBlockController extends Controller
{
    public function __construct(private readonly CaseStudyBlockService $blockService) {}

    public function index(CaseStudy $caseStudy, CaseStudySection $section): AnonymousResourceCollection
    {
        $this->authorize('view', $caseStudy);

        return CaseStudyBlockResource::collection($section->blocks()->with('points')->get());
    }

    /**
     * @throws Throwable
     */
    public function store(
        CaseStudyBlockStoreRequest $request,
        CaseStudy $caseStudy,
        CaseStudySection $section,
    ): CaseStudyBlockResource {
        $this->authorize('update', $caseStudy);

        $block = $this->blockService->create($section, $request->validated());

        return new CaseStudyBlockResource($block);
    }

    public function show(
        CaseStudy $caseStudy,
        CaseStudySection $section,
        CaseStudyBlock $block,
    ): CaseStudyBlockResource {
        $this->authorize('view', $caseStudy);

        $block->load('points');

        return new CaseStudyBlockResource($block);
    }

    /**
     * @throws Throwable
     */
    public function update(
        CaseStudyBlockUpdateRequest $request,
        CaseStudy $caseStudy,
        CaseStudySection $section,
        CaseStudyBlock $block,
    ): CaseStudyBlockResource {
        $this->authorize('update', $caseStudy);

        $block = $this->blockService->update($block, $request->validated());

        return new CaseStudyBlockResource($block);
    }

    /**
     * @throws Throwable
     */
    public function destroy(CaseStudy $caseStudy, CaseStudySection $section, CaseStudyBlock $block): Response
    {
        $this->authorize('update', $caseStudy);

        $this->blockService->delete($block);

        return response()->noContent();
    }
}
