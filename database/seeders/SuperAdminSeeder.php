<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin 1',
                'password' => Hash::make('123456789'), // غيّرها لاحقًا
                'email_verified_at' => now(),
                'organization_id' => null,
                'is_active' => true,
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'superadmin2@example.com'],
            [
                'name' => 'Super Admin 2',
                'password' => Hash::make('123456789'), // غيّرها لاحقًا
                'email_verified_at' => now(),
                'organization_id' => null,
                'is_active' => true,
            ]
        );

        $user1->assignRole('super_admin');
        $user2->assignRole('super_admin');
    }
}
