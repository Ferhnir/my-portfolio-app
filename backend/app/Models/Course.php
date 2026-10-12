<?php

namespace App\Models;

use App\Observers\CourseObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CourseObserver::class)]
class Course extends Model implements SortableModel
{
    use SoftDeletes;

    const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'name',
        'provider',
        'start_date',
        'end_date',
        'description',
        'url',
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
