<?php

namespace App\Models;

use App\HasUnassignedRelation;
use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory, HasUnassignedRelation;

    public function techStackPlacements(): HasMany
    {
        return $this->hasMany(TechStackPlacement::class);
    }
}
