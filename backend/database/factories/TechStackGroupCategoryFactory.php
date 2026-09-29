<?php

namespace Database\Factories;

use App\Models\TechStackCategory;
use App\Models\TechStackGroup;
use App\Models\TechStackGroupCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TechStackGroupCategory>
 */
class TechStackGroupCategoryFactory extends Factory
{
    protected $model = TechStackGroupCategory::class;

    public function definition(): array
    {
        return [
            'group_id' => TechStackGroup::factory(),
            'category_id' => TechStackCategory::factory(),
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
