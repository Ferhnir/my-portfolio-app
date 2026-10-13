<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducationHistoryStoreRequest;
use App\Http\Requests\EducationHistoryUpdateRequest;
use App\Http\Resources\EducationHistoryResource;
use App\Models\EducationHistory;
use App\Services\EducationHistoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class EducationHistoryController extends Controller
{
    public function __construct(private readonly EducationHistoryService $educationHistoryService) {}

    public function index(): AnonymousResourceCollection
    {
        return EducationHistoryResource::collection(
            EducationHistory::query()->orderBy(EducationHistory::SORTABLE_COLUMN)->get()
        );
    }

    /**
     * @throws Throwable
     */
    public function store(EducationHistoryStoreRequest $request): EducationHistoryResource
    {
        $educationHistory = $this->educationHistoryService->create($request->validated());

        return new EducationHistoryResource($educationHistory);
    }

    public function show(EducationHistory $educationHistory): EducationHistoryResource
    {
        return new EducationHistoryResource($educationHistory);
    }

    /**
     * @throws Throwable
     */
    public function update(
        EducationHistoryUpdateRequest $request,
        EducationHistory $educationHistory,
    ): EducationHistoryResource {
        $educationHistory = $this->educationHistoryService->update(
            educationHistory: $educationHistory,
            attributes: $request->validated(),
        );

        return new EducationHistoryResource($educationHistory);
    }

    /**
     * @throws Throwable
     */
    public function destroy(EducationHistory $educationHistory): Response
    {
        $this->educationHistoryService->delete($educationHistory);

        return response()->noContent();
    }
}

