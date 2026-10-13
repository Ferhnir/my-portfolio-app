<?php

namespace App\Http\Controllers;

use App\Http\Requests\CertificateStoreRequest;
use App\Http\Requests\CertificateUpdateRequest;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class CertificateController extends Controller
{
    public function __construct(private readonly CertificateService $certificateService) {}

    public function index(): AnonymousResourceCollection
    {
        return CertificateResource::collection(
            Certificate::query()->orderBy(Certificate::SORTABLE_COLUMN)->get()
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CertificateStoreRequest $request): CertificateResource
    {
        $certificate = $this->certificateService->create($request->validated());

        return new CertificateResource($certificate);
    }

    public function show(Certificate $certificate): CertificateResource
    {
        return new CertificateResource($certificate);
    }

    /**
     * @throws Throwable
     */
    public function update(CertificateUpdateRequest $request, Certificate $certificate): CertificateResource
    {
        $certificate = $this->certificateService->update(
            certificate: $certificate,
            attributes: $request->validated(),
        );

        return new CertificateResource($certificate);
    }

    /**
     * @throws Throwable
     */
    public function destroy(Certificate $certificate): Response
    {
        $this->certificateService->delete($certificate);

        return response()->noContent();
    }
}
