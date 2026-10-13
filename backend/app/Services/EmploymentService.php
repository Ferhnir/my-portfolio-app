<?php

namespace App\Services;

use App\Models\Employment;
use Illuminate\Support\Facades\DB;
use Throwable;

class EmploymentService
{
    /**
     * @throws Throwable
     */
    public function create(array $attributes): Employment
    {
        return DB::transaction(function () use ($attributes): Employment {
            $employment = Employment::query()->create($attributes);

            return $employment->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function update(Employment $employment, array $attributes): Employment
    {
        return DB::transaction(function () use ($employment, $attributes): Employment {
            $employment->update($attributes);

            return $employment->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(Employment $employment): void
    {
        DB::transaction(fn () => $employment->delete());
    }
}
