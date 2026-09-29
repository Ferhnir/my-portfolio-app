<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\TechStackGroupCategoryStoreRequest;
use App\Http\Requests\TechStackGroupCategoryUpdateRequest;
use App\Http\Resources\TechStackGroupCategoryResource;
use App\Models\TechStackCategory;
use App\Models\TechStackGroup;
use App\Models\TechStackGroupCategory;
use App\Services\TechStackGroupCategoryService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TechStackGroupCategoryController extends Controller
{
    /**
     * @throws ApiException
     */
    public function store(
        TechStackGroupCategoryStoreRequest $request,
        TechStackGroup $techStackGroup,
        TechStackCategory $techStackCategory
    ): TechStackGroupCategoryResource {

        try {

            $techStackGroupCategory = new TechStackGroupCategoryService()
                ->setTechStackGroup($techStackGroup)
                ->setTechStackCategory($techStackCategory)
                ->saveLink();

            $techStackGroupCategory->load([
                'techStackGroup',
                'techStackCategory'
            ]);

            return new TechStackGroupCategoryResource($techStackGroupCategory);

        } catch (\Exception $e) {
            throw new ApiException(
                message: __('exceptions.something_went_wrong'),
                status: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                errors: [$e->getMessage()],
            );
        }
    }

    /**
     * @throws ApiException
     * @throws \Throwable
     */
    public function update(
        TechStackGroupCategoryUpdateRequest $request,
        TechStackGroupCategory $techStackGroupCategory
    ): TechStackGroupCategoryResource
    {
        try {

            $techStackGroupCategoryService = new TechStackGroupCategoryService()
                ->loadConnector($techStackGroupCategory);

            $techStackGroupCategory = DB::transaction(function () use (
                $request, $techStackGroupCategory, $techStackGroupCategoryService
            ){
                if ($request->filled('group_id')) {
                    $techStackGroupCategoryService->setTechStackGroup($request->input('group_id'));
                }

                $techStackGroupCategoryService->setSortOrder($request->input('sort_order'));

                return $techStackGroupCategoryService->updateLink();
            });

            $techStackGroupCategory->load([
                'techStackGroup',
                'techStackCategory'
            ]);

            return new TechStackGroupCategoryResource($techStackGroupCategory);

        } catch (\Exception $e) {
            throw new ApiException(
                message: __('exceptions.something_went_wrong'),
                status: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                errors: [$e->getMessage()],
            );
        }
    }

    public function destroy(TechStackGroupCategory $techStackGroupCategory): Response
    {
        new TechStackGroupCategoryService()
            ->loadConnector($techStackGroupCategory)
            ->deleteLink();

        return response()->noContent();
    }
}
