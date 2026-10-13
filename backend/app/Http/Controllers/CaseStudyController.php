<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsCaseStudyVisibility;
use App\Http\Requests\CaseStudyStoreRequest;
use App\Http\Requests\CaseStudyUpdateRequest;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use App\Services\CaseStudyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class CaseStudyController extends Controller
{
    use GuardsCaseStudyVisibility;

    public function __construct(private readonly CaseStudyService $caseStudyService) {}

    public function index(): AnonymousResourceCollection
    {
        $query = CaseStudy::query()->orderBy(CaseStudy::SORTABLE_COLUMN);

        if (auth('sanctum')->user() === null) {
            $query->where('status', CaseStudy::STATUS_PUBLISHED);
        }

        return CaseStudyResource::collection($query->get());
    }

    /**
     * @throws Throwable
     */
    public function store(CaseStudyStoreRequest $request): CaseStudyResource
    {
        $caseStudy = $this->caseStudyService->create($request->validated());

        return new CaseStudyResource($caseStudy);
    }

    public function show(CaseStudy $caseStudy): CaseStudyResource
    {
        $this->guardCaseStudy($caseStudy);

        $caseStudy->load('sections.blocks.points');

        return new CaseStudyResource($caseStudy);
    }

    /**
     * @throws Throwable
     */
    public function update(CaseStudyUpdateRequest $request, CaseStudy $caseStudy): CaseStudyResource
    {
        $caseStudy = $this->caseStudyService->update($caseStudy, $request->validated());

        return new CaseStudyResource($caseStudy);
    }

    /**
     * @throws Throwable
     */
    public function destroy(CaseStudy $caseStudy): Response
    {
        $this->caseStudyService->delete($caseStudy);

        return response()->noContent();
    }
}
