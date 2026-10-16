<?php

namespace App\Models;

use App\Observers\AssetObserver;
use App\SortableModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(AssetObserver::class)]
class Asset extends Model implements SortableModel
{
    use SoftDeletes;

    public const string COLLECTION_LOGO = 'logo';

    public const string COLLECTION_COVER = 'cover';

    public const string COLLECTION_GALLERY = 'gallery';

    public const array COLLECTIONS = [
        self::COLLECTION_LOGO,
        self::COLLECTION_COVER,
        self::COLLECTION_GALLERY,
    ];

    public const array SINGLE_COLLECTIONS = [
        self::COLLECTION_LOGO,
        self::COLLECTION_COVER,
    ];

    public const string SORTABLE_COLUMN = 'sort_order';

    protected $fillable = [
        'collection',
        'disk',
        'archive_disk',
        'path',
        'display_path',
        'thumb_path',
        'original_name',
        'mime',
        'size',
        self::SORTABLE_COLUMN,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
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
            ->where('assetable_type', $this->assetable_type)
            ->where('assetable_id', $this->assetable_id)
            ->where('collection', $this->collection)
            ->whereKeyNot($this->getKey())
            ->orderBy(self::SORTABLE_COLUMN)
            ->get();
    }

    public function assetable(): MorphTo
    {
        return $this->morphTo();
    }
}
