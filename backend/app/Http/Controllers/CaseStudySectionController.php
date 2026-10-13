<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsCaseStudyVisibility;
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
    use GuardsCaseStudyVisibility;

    public function __construct(private readonly CaseStudySectionService $sectionService) {}

    public function index(CaseStudy $caseStudy): AnonymousResourceCollection
    {
        $this->guardCaseStudy($caseStudy);

        $sections = $caseStudy->sections()->with('blocks.points')->get();

        return CaseStudySectionResource::collection($sections);
    }

    /**
     * @throws Throwable
     */
    public function store(CaseStudySectionStoreRequest $request, CaseStudy $caseStudy): CaseStudySectionResource
    {
        $section = $this->sectionService->create($caseStudy, $request->validated());

        return new CaseStudySectionResource($section);
    }

    public function show(CaseStudy $caseStudy, CaseStudySection $section): CaseStudySectionResource
    {
        $this->guardCaseStudy($caseStudy);

        $section->load('blocks.points');

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
        $section = $this->sectionService->update($section, $request->validated());

        return new CaseStudySectionResource($section);
    }

    /**
     * @throws Throwable
     */
    public function destroy(CaseStudy $caseStudy, CaseStudySection $section): Response
    {
        $this->sectionService->delete($section);

        return response()->noContent();
    }
}
