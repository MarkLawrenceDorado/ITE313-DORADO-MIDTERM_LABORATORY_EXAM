<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Breeze's factory password is "password"
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        Account::create([
            'user_id'        => $user->id,
            'account_number' => 'ACC-0001',
            'balance'        => 500,
        ]);
    }
}
