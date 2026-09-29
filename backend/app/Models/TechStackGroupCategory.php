<?php

namespace App\Models;

use App\Observers\TechStackGroupCategoryObserver;
use Database\Factories\TechStackGroupCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['group_id', 'category_id', 'sort_order'])]
#[ObservedBy(TechStackGroupCategoryObserver::class)]
class TechStackGroupCategory extends Model
{
    /** @use HasFactory<TechStackGroupCategoryFactory> */
    use HasFactory;

    protected $table = 'tech_stack_group_category';

    public function techStackGroup(): BelongsTo
    {
        return $this->belongsTo(TechStackGroup::class, 'group_id');
    }

    public function techStackCategory(): BelongsTo
    {
        return $this->belongsTo(TechStackCategory::class, 'category_id');
    }

    public function techStackPlacements(): HasMany
    {
        return $this->hasMany(TechStackPlacement::class, 'group_category_id')->orderBy('sort_order');
    }
}
