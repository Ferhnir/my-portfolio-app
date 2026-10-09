<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionNamedType;

trait HasUnassignedRelation
{
    /**
     * @param  Builder<static>  $query
     */
    public function scopeUnassigned(Builder $query, Request $request, string $relation): void
    {
        if (! $this->hasUnassignedRelation($relation)) {
            throw new InvalidArgumentException(sprintf(
                '%s::%s() is not an Eloquent relation.',
                static::class,
                $relation,
            ));
        }

        $query->when(
            $request->filled('unassigned'),
            fn (Builder $query) => $query->whereDoesntHave($relation),
        );
    }

    private function hasUnassignedRelation(string $relation): bool
    {
        if (! method_exists($this, $relation)) {
            return false;
        }

        $type = (new ReflectionMethod($this, $relation))->getReturnType();

        return $type instanceof ReflectionNamedType
            && ! $type->isBuiltin()
            && is_a($type->getName(), Relation::class, true);
    }
}
