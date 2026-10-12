<?php

namespace App\Models;

use App\Observers\CertificateObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CertificateObserver::class)]
class Certificate extends Model implements SortableModel
{
    use SoftDeletes;

    const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'name',
        'issuer',
        'issued_at',
        'expires_at',
        'credential_id',
        'credential_url',
        'description',
        self::SORTABLE_COLUMN,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
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
