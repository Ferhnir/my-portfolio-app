<?php

namespace App\Models;

use Database\Factories\TechStackPlacementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['group_category_id', 'technology_id', 'sort_order'])]
class TechStackPlacement extends Model
{
    /** @use HasFactory<TechStackPlacementFactory> */
    use HasFactory;

    protected $table = 'tech_stack_placements';

    public function techStackGroupCategory(): BelongsTo
    {
        return $this->belongsTo(TechStackGroupCategory::class, 'group_category_id');
    }

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }
}
