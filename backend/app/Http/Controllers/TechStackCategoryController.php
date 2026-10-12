<?php

namespace App\Http\Controllers;

use App\Http\Resources\TechStackCategoryResource;
use App\Models\TechStackCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class TechStackCategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return TechStackCategoryResource::collection(
            TechStackCategory::query()
                ->unassigned($request, 'techStackGroupLinks')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request): TechStackCategoryResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category = TechStackCategory::query()->create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return new TechStackCategoryResource($category);
    }

    public function show(TechStackCategory $techStackCategory): TechStackCategoryResource
    {
        return new TechStackCategoryResource($techStackCategory);
    }

    public function update(Request $request, TechStackCategory $techStackCategory): TechStackCategoryResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $techStackCategory->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return new TechStackCategoryResource($techStackCategory);
    }

    public function destroy(TechStackCategory $techStackCategory): Response
    {
        $techStackCategory->delete();

        return response()->noContent();
    }
}
