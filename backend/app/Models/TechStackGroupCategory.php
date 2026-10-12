<?php

namespace App\Models;

use App\ModelLinkInterface;
use App\Observers\TechStackGroupCategoryObserver;
use App\SortableModel;
use App\Traits\Traits\IsSortable;
use Database\Factories\TechStackGroupCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(TechStackGroupCategoryObserver::class)]
class TechStackGroupCategory extends Model implements SortableModel, ModelLinkInterface
{
    /** @use HasFactory<TechStackGroupCategoryFactory> */
    use HasFactory;
    use IsSortable;

    const string LINKABLE_MODEL_PARENT_KEY = 'group_id';
    const string LINKABLE_MODEL_KEY = 'category_id';
    const string SORTABLE_COLUMN = 'sort_order';

    protected $table = 'tech_stack_group_category';

    protected $fillable = [
        self::LINKABLE_MODEL_PARENT_KEY,
        self::LINKABLE_MODEL_KEY,
        self::SORTABLE_COLUMN,
    ];

    public function techStackGroup(): BelongsTo
    {
        return $this->belongsTo(related: TechStackGroup::class, foreignKey: self::LINKABLE_MODEL_PARENT_KEY);
    }

    public function techStackCategory(): BelongsTo
    {
        return $this->belongsTo(related: TechStackCategory::class, foreignKey: self::LINKABLE_MODEL_KEY);
    }

    public function techStackPlacements(): HasMany
    {
        return $this->hasMany(related: TechStackPlacement::class, foreignKey: 'group_category_id')
            ->orderBy(column: self::SORTABLE_COLUMN);
    }

    public function parent(): BelongsTo
    {
        return $this->techStackGroup();
    }
}
