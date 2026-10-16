<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseStudyPointStoreRequest;
use App\Http\Requests\CaseStudyPointUpdateRequest;
use App\Http\Resources\CaseStudyPointResource;
use App\Models\CaseStudy;
use App\Models\CaseStudyBlock;
use App\Models\CaseStudyPoint;
use App\Models\CaseStudySection;
use App\Services\CaseStudyPointService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class CaseStudyPointController extends Controller
{
    public function __construct(private readonly CaseStudyPointService $pointService) {}

    public function index(
        CaseStudy $caseStudy,
        CaseStudySection $section,
        CaseStudyBlock $block,
    ): AnonymousResourceCollection {
        $this->authorize('view', $caseStudy);

        return CaseStudyPointResource::collection($block->points()->get());
    }

    /**
     * @throws Throwable
     */
    public function store(
        CaseStudyPointStoreRequest $request,
        CaseStudy $caseStudy,
        CaseStudySection $section,
        CaseStudyBlock $block,
    ): CaseStudyPointResource {
        $this->authorize('update', $caseStudy);

        $point = $this->pointService->create($block, $request->validated());

        return new CaseStudyPointResource($point);
    }

    public function show(
        CaseStudy $caseStudy,
        CaseStudySection $section,
        CaseStudyBlock $block,
        CaseStudyPoint $point,
    ): CaseStudyPointResource {
        $this->authorize('view', $caseStudy);

        return new CaseStudyPointResource($point);
    }

    /**
     * @throws Throwable
     */
    public function update(
        CaseStudyPointUpdateRequest $request,
        CaseStudy $caseStudy,
        CaseStudySection $section,
        CaseStudyBlock $block,
        CaseStudyPoint $point,
    ): CaseStudyPointResource {
        $this->authorize('update', $caseStudy);

        $point = $this->pointService->update($point, $request->validated());

        return new CaseStudyPointResource($point);
    }

    /**
     * @throws Throwable
     */
    public function destroy(
        CaseStudy $caseStudy,
        CaseStudySection $section,
        CaseStudyBlock $block,
        CaseStudyPoint $point,
    ): Response {
        $this->authorize('update', $caseStudy);

        $this->pointService->delete($point);

        return response()->noContent();
    }
}
