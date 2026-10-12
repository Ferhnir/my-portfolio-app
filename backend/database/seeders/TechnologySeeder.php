<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TechStackSeeder::class);
    }
}
