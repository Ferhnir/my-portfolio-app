<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmploymentStoreRequest;
use App\Http\Requests\EmploymentUpdateRequest;
use App\Http\Resources\EmploymentResource;
use App\Models\Employment;
use App\Services\EmploymentService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class EmploymentController extends Controller
{
    public function __construct(private readonly EmploymentService $employmentService) {}

    public function index(): AnonymousResourceCollection
    {
        return EmploymentResource::collection(
            Employment::query()->orderBy(Employment::SORTABLE_COLUMN)->get()
        );
    }

    /**
     * @throws Throwable
     */
    public function store(EmploymentStoreRequest $request): EmploymentResource
    {
        $employment = $this->employmentService->create($request->validated());

        return new EmploymentResource($employment);
    }

    public function show(Employment $employment): EmploymentResource
    {
        return new EmploymentResource($employment);
    }

    /**
     * @throws Throwable
     */
    public function update(EmploymentUpdateRequest $request, Employment $employment): EmploymentResource
    {
        $employment = $this->employmentService->update(
            employment: $employment,
            attributes: $request->validated(),
        );

        return new EmploymentResource($employment);
    }

    /**
     * @throws Throwable
     */
    public function destroy(Employment $employment): Response
    {
        $this->employmentService->delete($employment);

        return response()->noContent();
    }
}
