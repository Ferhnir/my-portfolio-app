<?php

namespace App\Models;

use Database\Factories\TechStackCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class TechStackCategory extends Model
{
    /** @use HasFactory<TechStackCategoryFactory> */
    use HasFactory;

    protected $table = 'tech_stack_categories';

    public function techStackGroupCategories(): HasMany
    {
        return $this->hasMany(TechStackGroupCategory::class, 'category_id');
    }
}
