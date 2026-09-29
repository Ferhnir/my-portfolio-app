<?php

namespace Database\Factories;

use App\Models\TechStackGroupCategory;
use App\Models\TechStackPlacement;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TechStackPlacement>
 */
class TechStackPlacementFactory extends Factory
{
    protected $model = TechStackPlacement::class;

    public function definition(): array
    {
        return [
            'group_category_id' => TechStackGroupCategory::factory(),
            'technology_id' => Technology::factory(),
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
