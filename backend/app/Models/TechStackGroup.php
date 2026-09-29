<?php

namespace App\Models;

use App\Observers\TechStackGroupObserver;
use Database\Factories\TechStackGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'sort_order'])]
#[ObservedBy(TechStackGroupObserver::class)]
class TechStackGroup extends Model
{
    /** @use HasFactory<TechStackGroupFactory> */
    use HasFactory;

    protected $table = 'tech_stack_groups';

    public function techStackGroupCategories(): HasMany
    {
        return $this->hasMany(TechStackGroupCategory::class, 'group_id')->orderBy('sort_order');
    }
}
