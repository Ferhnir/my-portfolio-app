<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\TechStackGroupCategoryStoreRequest;
use App\Http\Requests\TechStackGroupCategoryUpdateRequest;
use App\Http\Resources\TechStackGroupCategoryResource;
use App\Models\TechStackCategory;
use App\Models\TechStackGroup;
use App\Models\TechStackGroupCategory;
use App\Services\ModelLinkService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use Exception;

class TechStackGroupCategoryController extends Controller
{
    /**
     * @throws ApiException|Throwable
     */
    public function store(
        TechStackGroupCategoryStoreRequest $request,
        TechStackGroup $techStackGroup,
        TechStackCategory $techStackCategory
    ): TechStackGroupCategoryResource {

        try {

            $modelLinkService = new ModelLinkService(new TechStackGroupCategory())
                ->setLinkModel(
                    linkableModel: $techStackGroup,
                    linkModelKey: TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY
                )
                ->setLinkModel(
                    linkableModel: $techStackCategory,
                    linkModelKey: TechStackGroupCategory::LINKABLE_MODEL_KEY
                )
                ->setSortOrder($request->input(key: TechStackGroupCategory::SORTABLE_COLUMN));

            $techStackGroupCategory = DB::transaction( fn() => $modelLinkService->createLink());


            $techStackGroupCategory->load([
                'techStackGroup',
                'techStackCategory',
            ]);

            return new TechStackGroupCategoryResource($techStackGroupCategory);

        } catch (Exception|Throwable $e) {
            throw new ApiException(
                message: __('exceptions.something_went_wrong'),
                status: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                errors: [$e->getMessage()],
            );
        }
    }

    /**
     * @throws ApiException
     * @throws Throwable
     */
    public function update(
        TechStackGroupCategoryUpdateRequest $request,
        TechStackGroupCategory $techStackGroupCategory
    ): TechStackGroupCategoryResource {

        try {

            $modelLinkService = new ModelLinkService($techStackGroupCategory);

            //Re-assign category to different group
            if ($request->filled(TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY)) {

                $linkableModel = TechStackGroup::query()->findOrFail(
                    $request->input(TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY)
                );

                $modelLinkService->setLinkModel(
                    linkableModel: $linkableModel,
                    linkModelKey: TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY
                );
            }

            //Update Sort Order vba
            if ($request->filled(TechStackGroupCategory::SORTABLE_COLUMN)) {
                $modelLinkService->setSortOrder($request->input(TechStackGroupCategory::SORTABLE_COLUMN));
            }

            $techStackGroupCategory = DB::transaction(fn () => $modelLinkService->updateLink());

            $techStackGroupCategory->load([
                'techStackGroup',
                'techStackCategory',
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
     * @throws Throwable
     */
    public function destroy(TechStackGroupCategory $techStackGroupCategory): Response
    {
        DB::transaction(fn () => $techStackGroupCategory->delete());

        return response()->noContent();
    }
}
