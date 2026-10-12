<?php

namespace App\Models;

use App\Observers\TechStackGroupObserver;
use App\SortableModel;
use Database\Factories\TechStackGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'slug', 'sort_order'])]
#[ObservedBy(TechStackGroupObserver::class)]
class TechStackGroup extends Model
{
    /** @use HasFactory<TechStackGroupFactory> */
    use HasFactory;

    protected $table = 'tech_stack_groups';

    public function techStackGroupLinks(): HasMany
    {
        return $this->hasMany(
            related: TechStackGroupCategory::class,
            foreignKey: TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY
        )
            ->orderBy(column: TechStackGroupCategory::SORTABLE_COLUMN);
    }

    public function techStackCategories(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: TechStackCategory::class,
            through: TechStackGroupCategory::class
        )->orderBy(column: TechStackGroupCategory::SORTABLE_COLUMN);
    }
}
