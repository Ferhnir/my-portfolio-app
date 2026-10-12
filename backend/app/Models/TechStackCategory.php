<?php

namespace App\Models;

use App\HasUnassignedRelation;
use Database\Factories\TechStackCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[Fillable(['name', 'slug'])]
class TechStackCategory extends Model
{
    /** @use HasFactory<TechStackCategoryFactory> */
    use HasFactory, HasUnassignedRelation;

    protected $table = 'tech_stack_categories';

    public function techStackGroupLinks(): HasMany
    {
        return $this->hasMany(TechStackGroupCategory::class, 'category_id');
    }

    public function parentGroup(): HasOneThrough
    {
        return $this->hasOneThrough(
            related: TechStackGroup::class,
            through: TechStackGroupCategory::class,
        );
    }

    public function siblings(TechStackGroup $techStackGroup): HasManyThrough
    {
        return $techStackGroup->hasManyThrough(
            related: TechStackCategory::class,
            through: TechStackGroupCategory::class,
            firstKey: 'group_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'category_id',
        )
            ->whereKeyNot($this->getKey())
            ->orderBy('tech_stack_group_category.sort_order');
    }
}
