<?php

namespace App\Http\Controllers;

use App\Http\Requests\TechnologyStoreRequest;
use App\Http\Resources\TechnologyResource;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class TechnologyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TechnologyResource::collection(
            Technology::query()->orderBy('name')->get()
        );
    }

    public function store(TechnologyStoreRequest $request): TechnologyResource
    {
        $technology = Technology::query()->create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
        ]);

        return new TechnologyResource($technology);
    }

    public function show(Technology $technology): TechnologyResource
    {
        return new TechnologyResource($technology);
    }

    public function update(Request $request, Technology $technology): TechnologyResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $technology->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return new TechnologyResource($technology);
    }

    public function destroy(Technology $technology): Response
    {
        $technology->delete();

        return response()->noContent();
    }
}
