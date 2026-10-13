<?php

namespace App\Models;

use App\Observers\CaseStudyBlockObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CaseStudyBlockObserver::class)]
class CaseStudyBlock extends Model implements SortableModel
{
    use SoftDeletes;

    public const string LAYOUT_CARD = 'card';

    public const string LAYOUT_GROUP = 'group';

    public const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'layout',
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
        return 'case_study_section_id';
    }

    public function siblings(): Collection
    {
        return $this->newQuery()
            ->where($this->sortParentKey(), $this->getAttribute($this->sortParentKey()))
            ->whereKeyNot($this->getKey())
            ->orderBy(self::SORTABLE_COLUMN)
            ->get();
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CaseStudySection::class, 'case_study_section_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(CaseStudyPoint::class)->orderBy(CaseStudyPoint::SORTABLE_COLUMN);
    }
}
