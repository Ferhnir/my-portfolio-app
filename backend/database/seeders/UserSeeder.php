<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = array([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        foreach ($users as $user) {
            $userModel = User::query()->firstOrCreate($user);
        }
    }
}
