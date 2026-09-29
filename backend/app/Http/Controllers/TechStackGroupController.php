<?php

namespace App\Http\Controllers;

use App\Http\Requests\TechStackGroupStoreRequest;
use App\Http\Requests\TechStackGroupUpdateRequest;
use App\Http\Resources\TechStackGroupResource;
use App\Models\TechStackGroup;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;

class TechStackGroupController extends Controller
{
    public function index(): JsonResponse
    {
        $payload = Cache::rememberForever('tech-stack.index', function () {
            $groups = TechStackGroup::query()
                ->orderBy('sort_order')
                ->get();
            return TechStackGroupResource::collection($groups)->resolve();
        });

        return response()->json(['data' => $payload]);
    }

    public function store(TechStackGroupStoreRequest $request): TechStackGroupResource
    {
        $data = $request->validated();

        $group = TechStackGroup::query()->create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return new TechStackGroupResource($group);
    }

    public function show(TechStackGroup $techStackGroup): TechStackGroupResource
    {
        $techStackGroup->load(
            relations: [
                'techStackGroupCategories.techStackCategory',
                'techStackGroupCategories.techStackPlacements.technology',
            ]
        );

        return new TechStackGroupResource($techStackGroup);
    }

    public function update(TechStackGroupUpdateRequest $request, TechStackGroup $techStackGroup): TechStackGroupResource
    {
        $techStackGroup->update($request->validated());

        return new TechStackGroupResource($techStackGroup);
    }

    public function destroy(TechStackGroup $techStackGroup): Response
    {
        $techStackGroup->delete();

        return response()->noContent();
    }
}
