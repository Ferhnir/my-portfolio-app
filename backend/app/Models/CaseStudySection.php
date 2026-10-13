<?php

namespace App\Models;

use App\Observers\CaseStudySectionObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CaseStudySectionObserver::class)]
class CaseStudySection extends Model implements SortableModel
{
    use SoftDeletes;

    public const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'title',
        'introduction',
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

    public function sortParentKey(): string
    {
        return 'case_study_id';
    }

    public function siblings(): Collection
    {
        return $this->newQuery()
            ->where($this->sortParentKey(), $this->getAttribute($this->sortParentKey()))
            ->whereKeyNot($this->getKey())
            ->orderBy(self::SORTABLE_COLUMN)
            ->get();
    }

    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(CaseStudyBlock::class)->orderBy(CaseStudyBlock::SORTABLE_COLUMN);
    }
}
