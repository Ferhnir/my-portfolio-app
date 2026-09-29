<?php

namespace App\Services;

use App\Models\TechStackCategory;
use App\Models\TechStackGroup;
use App\Models\TechStackGroupCategory;

class TechStackGroupCategoryService
{
    private TechStackGroupCategory $techStackGroupCategory;

    private TechStackGroup $techStackGroup;

    private TechStackCategory $techStackCategory;

    private ?int $sortOrder;

    public function __construct()
    {
        $this->techStackGroupCategory = new TechStackGroupCategory;
        $this->techStackGroup = new TechStackGroup;
        $this->techStackCategory = new TechStackCategory;
        $this->sortOrder = null;
    }

    public function loadConnector(TechStackGroupCategory $techStackGroupCategory): self
    {
        $this->techStackGroupCategory = $techStackGroupCategory;

        return $this;
    }

    public function setTechStackGroup(int|TechStackGroup $techStackGroup): self
    {
        if (! $techStackGroup instanceof TechStackGroup) {
            $techStackGroup = TechStackGroup::query()->findOrFail($techStackGroup);
        }

        $this->techStackGroup = $techStackGroup;

        return $this;
    }

    public function setTechStackCategory(TechStackCategory $techStackCategory): self
    {
        $this->techStackCategory = $techStackCategory;

        return $this;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    private static function reShuffle(TechStackGroup $techStackGroup, int $oldOrder, int $newOrder): void
    {
        if ($newOrder === $oldOrder) {
            return;
        }

        $categories = $techStackGroup->techStackGroupCategories();

        if ($newOrder < $oldOrder) {
            $categories
                ->where('sort_order', '>=', $newOrder)
                ->where('sort_order', '<', $oldOrder)
                ->increment('sort_order');

            return;
        }

        $categories
            ->where('sort_order', '>', $oldOrder)
            ->where('sort_order', '<=', $newOrder)
            ->decrement('sort_order');
    }

    private function setAttributes(): array
    {
        $attributes = ['category_id' => $this->techStackCategory->getAttribute('id')];

        if (is_null($this->sortOrder)) {
            $attributes['sort_order'] = $this->techStackGroup->techStackGroupCategories()->max('sort_order') + 1;
        }

        if ($this->techStackGroup->exists) {
            $attributes['group_id'] = $this->techStackGroup->getAttribute('id');
        }

        return $attributes;
    }

    public function saveLink(): TechStackGroupCategory
    {
        $this->techStackGroupCategory->fill($this->setAttributes())->save();

        return $this->techStackGroupCategory;
    }

    public function updateLink(): TechStackGroupCategory
    {
        $oldOrder = (int) $this->techStackGroupCategory->getAttribute('sort_order');
        $currentGroup = $this->techStackGroupCategory->techStackGroup;
        $destination = $this->techStackGroup->exists ? $this->techStackGroup : $currentGroup;

        if ($destination->id !== $currentGroup->id) {
            $currentGroup->techStackGroupCategories()
                ->where('sort_order', '>', $oldOrder)
                ->decrement('sort_order');

            if ($this->sortOrder !== null) {
                $destination->techStackGroupCategories()
                    ->where('sort_order', '>=', $this->sortOrder)
                    ->increment('sort_order');

                $this->techStackGroupCategory->setAttribute('sort_order', $this->sortOrder);
            }

            $this->techStackGroupCategory->setAttribute('group_id', $destination->id);
        } elseif ($this->sortOrder !== null && $oldOrder !== $this->sortOrder) {
            self::reShuffle(
                techStackGroup: $currentGroup,
                oldOrder: $oldOrder,
                newOrder: $this->sortOrder,
            );

            $this->techStackGroupCategory->setAttribute('sort_order', $this->sortOrder);
        }

        $this->techStackGroupCategory->save();

        return $this->techStackGroupCategory->refresh();
    }

    public function deleteLink(): bool
    {
        return $this->techStackGroupCategory->delete();
    }
}
