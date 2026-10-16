<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseStudyStoreRequest;
use App\Http\Requests\CaseStudyUpdateRequest;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use App\Services\CaseStudyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Throwable;

class CaseStudyController extends Controller
{
    public function __construct(private readonly CaseStudyService $caseStudyService) {}

    public function index(): AnonymousResourceCollection
    {
        $query = CaseStudy::query()->with('assets')->orderBy(CaseStudy::SORTABLE_COLUMN);

        if (Gate::denies('viewDrafts', CaseStudy::class)) {
            $query->where('status', CaseStudy::STATUS_PUBLISHED);
        }

        return CaseStudyResource::collection($query->get());
    }

    /**
     * @throws Throwable
     */
    public function store(CaseStudyStoreRequest $request): CaseStudyResource
    {
        $this->authorize('create', CaseStudy::class);

        $caseStudy = $this->caseStudyService->create($request->validated());

        return new CaseStudyResource($caseStudy);
    }

    public function show(CaseStudy $caseStudy): CaseStudyResource
    {
        $this->authorize('view', $caseStudy);

        $caseStudy->load(['assets', 'sections.assets', 'sections.blocks.points']);

        return new CaseStudyResource($caseStudy);
    }

    /**
     * @throws Throwable
     */
    public function update(CaseStudyUpdateRequest $request, CaseStudy $caseStudy): CaseStudyResource
    {
        $this->authorize('update', $caseStudy);

        $caseStudy = $this->caseStudyService->update($caseStudy, $request->validated());

        return new CaseStudyResource($caseStudy);
    }

    /**
     * @throws Throwable
     */
    public function destroy(CaseStudy $caseStudy): Response
    {
        $this->authorize('delete', $caseStudy);

        $this->caseStudyService->delete($caseStudy);

        return response()->noContent();
    }
}
