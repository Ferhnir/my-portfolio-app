<?php

namespace App\Services;

use App\ModelLinkInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelLinkService
{
    private array $attributes = [];
    private Model&ModelLinkInterface $linkModel;

    public function __construct(
        Model&ModelLinkInterface $model
    )
    {
        $this->linkModel = $model;
    }

    public function setLinkModel(Model $linkableModel, string $linkModelKey): self
    {
        $this->setLinkAttributes([
            'key' => $linkModelKey,
            'value' => $linkableModel->getAttribute($linkableModel->getKeyName()),
        ]);

        return $this;
    }
    public function setSortOrder(int $sortOrder = 1): self
    {
        $this->setLinkAttributes([
            'key' => 'sort_order',
            'value' => $sortOrder,
        ]);

        return $this;
    }

    private function setLinkAttributes(array $attribute = []): void
    {
        if (empty($attribute)) return;

        Arr::set($this->attributes, $attribute['key'], $attribute['value']);
    }

    public function createLink(): ModelLinkInterface
    {
        $this->linkModel->fill($this->attributes)->save();

        return $this->linkModel;
    }

    public function updateLink(): ModelLinkInterface
    {
        $this->linkModel->update($this->attributes);

        return $this->linkModel->refresh();
    }
}