<?php

namespace Database\Factories;

use App\Models\TechStackCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TechStackCategory>
 */
class TechStackCategoryFactory extends Factory
{
    protected $model = TechStackCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
