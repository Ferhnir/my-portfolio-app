<?php

namespace App\Http\Controllers;

use App\Http\Resources\TechStackGroupResource;
use App\Models\TechStackGroup;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TechStackController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $groups = TechStackGroup::query()
            ->with([
                'techStackGroupCategories.techStackCategory',
                'techStackGroupCategories.techStackPlacements.technology',
            ])
            ->orderBy('sort_order')
            ->get();

        return TechStackGroupResource::collection($groups);
    }
}
