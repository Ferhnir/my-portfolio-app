<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseStudySectionStoreRequest;
use App\Http\Requests\CaseStudySectionUpdateRequest;
use App\Http\Resources\CaseStudySectionResource;
use App\Models\CaseStudy;
use App\Models\CaseStudySection;
use App\Services\CaseStudySectionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class CaseStudySectionController extends Controller
{
    public function __construct(private readonly CaseStudySectionService $sectionService) {}

    public function index(CaseStudy $caseStudy): AnonymousResourceCollection
    {
        $this->authorize('view', $caseStudy);

        $sections = $caseStudy->sections()->with(['assets', 'blocks.points'])->get();

        return CaseStudySectionResource::collection($sections);
    }

    /**
     * @throws Throwable
     */
    public function store(CaseStudySectionStoreRequest $request, CaseStudy $caseStudy): CaseStudySectionResource
    {
        $this->authorize('update', $caseStudy);

        $section = $this->sectionService->create($caseStudy, $request->validated());

        return new CaseStudySectionResource($section);
    }

    public function show(CaseStudy $caseStudy, CaseStudySection $section): CaseStudySectionResource
    {
        $this->authorize('view', $caseStudy);

        $section->load(['assets', 'blocks.points']);

        return new CaseStudySectionResource($section);
    }

    /**
     * @throws Throwable
     */
    public function update(
        CaseStudySectionUpdateRequest $request,
        CaseStudy $caseStudy,
        CaseStudySection $section,
    ): CaseStudySectionResource {
        $this->authorize('update', $caseStudy);

        $section = $this->sectionService->update($section, $request->validated());

        return new CaseStudySectionResource($section);
    }

    /**
     * @throws Throwable
     */
    public function destroy(CaseStudy $caseStudy, CaseStudySection $section): Response
    {
        $this->authorize('update', $caseStudy);

        $this->sectionService->delete($section);

        return response()->noContent();
    }
}
