<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $certificates = [
            [
                'name' => 'Example Certificate',
                'issuer' => 'Example Issuer',
                'issued_at' => '2021-06-01',
                'expires_at' => '2024-06-01',
                'credential_id' => 'EX-001',
                'credential_url' => 'https://example.com/certificates/ex-001',
                'description' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Example Badge',
                'issuer' => null,
                'issued_at' => '2023-01-10',
                'expires_at' => null,
                'credential_id' => null,
                'credential_url' => null,
                'description' => null,
                'sort_order' => 2,
            ],
        ];

        foreach ($certificates as $certificate) {
            Certificate::query()->updateOrCreate(
                ['name' => $certificate['name']],
                $certificate,
            );
        }
    }
}
