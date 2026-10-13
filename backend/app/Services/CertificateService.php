<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\DB;
use Throwable;

class CertificateService
{
    /**
     * @throws Throwable
     */
    public function create(array $attributes): Certificate
    {
        return DB::transaction(function () use ($attributes): Certificate {
            $certificate = Certificate::query()->create($attributes);

            return $certificate->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function update(Certificate $certificate, array $attributes): Certificate
    {
        return DB::transaction(function () use ($certificate, $attributes): Certificate {
            $certificate->update($attributes);

            return $certificate->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(Certificate $certificate): void
    {
        DB::transaction(fn () => $certificate->delete());
    }
}
