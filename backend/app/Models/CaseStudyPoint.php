<?php

namespace App\Models;

use App\Observers\CaseStudyPointObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CaseStudyPointObserver::class)]
class CaseStudyPoint extends Model implements SortableModel
{
    use SoftDeletes;

    public const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'body',
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
        return 'case_study_block_id';
    }

    public function siblings(): Collection
    {
        return $this->newQuery()
            ->where($this->sortParentKey(), $this->getAttribute($this->sortParentKey()))
            ->whereKeyNot($this->getKey())
            ->orderBy(self::SORTABLE_COLUMN)
            ->get();
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(CaseStudyBlock::class, 'case_study_block_id');
    }
}
