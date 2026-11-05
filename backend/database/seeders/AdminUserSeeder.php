<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command->error('ADMIN_EMAIL dan ADMIN_PASSWORD wajib diisi di file .env');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Admin Bynest'),
                'password' => $password, // otomatis di-hash oleh cast 'hashed'
            ]
        );
    }
}