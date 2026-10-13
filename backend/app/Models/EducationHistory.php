<?php

namespace App\Models;

use App\Observers\EducationHistoryObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(EducationHistoryObserver::class)]
class EducationHistory extends Model implements SortableModel
{
    use SoftDeletes;

    const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'school_name',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'is_current',
        'description',
        self::SORTABLE_COLUMN,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
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
}
