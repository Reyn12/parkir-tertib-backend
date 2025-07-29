<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'reynald',
                'email' => 'reynald@example.com',
                'password' => Hash::make('qweqweqwe'),
                'phone_number' => '081234567890',
                'profile_picture' => null,
            ],
            [
                'username' => 'budi',
                'email' => 'budi@example.com',
                'password' => Hash::make('qweqweqwe'),
                'phone_number' => '081234567891',
                'profile_picture' => null,
            ],
            [
                'username' => 'sari',
                'email' => 'sari@example.com',
                'password' => Hash::make('qweqweqwe'),
                'phone_number' => '081234567892',
                'profile_picture' => null,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
