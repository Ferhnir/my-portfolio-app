<?php

namespace App\Http\Controllers;

use App\Http\Resources\TechStackPlacementResource;
use App\Models\TechStackPlacement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class TechStackPlacementController extends Controller
{
    public function store(Request $request): TechStackPlacementResource
    {
        $data = $request->validate([
            'group_category_id' => ['required', 'integer', 'exists:tech_stack_group_category,id'],
            'technology_id' => [
                'required',
                'integer',
                'exists:technologies,id',
                Rule::unique('tech_stack_placements')->where('group_category_id', $request->integer('group_category_id')),
            ],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $placement = TechStackPlacement::query()->create([
            'group_category_id' => $data['group_category_id'],
            'technology_id' => $data['technology_id'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $placement->load('technology');

        return new TechStackPlacementResource($placement);
    }

    public function update(Request $request, TechStackPlacement $techStackPlacement): TechStackPlacementResource
    {
        $data = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $techStackPlacement->update($data);
        $techStackPlacement->load('technology');

        return new TechStackPlacementResource($techStackPlacement);
    }

    public function destroy(TechStackPlacement $techStackPlacement): Response
    {
        $techStackPlacement->delete();

        return response()->noContent();
    }
}
