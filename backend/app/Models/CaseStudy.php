<?php

namespace App\Models;

use App\Observers\CaseStudyObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CaseStudyObserver::class)]
class CaseStudy extends Model implements SortableModel
{
    use SoftDeletes;

    public const string STATUS_DRAFT = 'draft';

    public const string STATUS_PUBLISHED = 'published';

    public const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'title',
        'slug',
        'client_name',
        'tagline',
        'summary',
        'introduction',
        'status',
        self::SORTABLE_COLUMN,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            self::SORTABLE_COLUMN => 'integer',
        ];
    }

    public function getSortableColumnName(): string
    {
        return self::SORTABLE_COLUMN;
    }

    public function siblings(): Collection
    {
        return $this->newQuery()
            ->whereKeyNot($this->getKey())
            ->orderBy(self::SORTABLE_COLUMN)
            ->get();
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CaseStudySection::class)->orderBy(CaseStudySection::SORTABLE_COLUMN);
    }

    public function assets(): MorphMany
    {
        return $this->morphMany(Asset::class, 'assetable')
            ->orderBy('collection')
            ->orderBy(Asset::SORTABLE_COLUMN);
    }
}
