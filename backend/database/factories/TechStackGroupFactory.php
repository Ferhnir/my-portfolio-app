<?php

namespace Database\Factories;

use App\Models\TechStackGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TechStackGroup>
 */
class TechStackGroupFactory extends Factory
{
    protected $model = TechStackGroup::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
