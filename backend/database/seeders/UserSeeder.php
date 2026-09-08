<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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
                'name' => 'Reginal Kencana',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'no_hp' => '081234567890',
                'alamat' => 'Bandung, West java',
            ],
            [
                'name' => 'Dimas Laher',
                'email' => 'petugas@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'no_hp' => '082345678901',
                'alamat' => 'Cangkring, Baleendah',
            ],
            [
                'name' => 'Hafidz', 
                'email' => 'hafidz@gmail.com',
                'password' => Hash::make ('password123'),
                'role' => 'peminjam',
                'no_hp' => '083456789012',
                'alamat' => 'Jelekong, Bandung',
            ],
            [
                'name' => 'Handy Bernard',
                'email' => 'handy@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '084567890123',
                'alamat' => 'Bsi, Balendah',
            ],
            [
                'name' => 'Rubby Setrum',
                'email' => 'rubby@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '085678901234',
                'alamat' => 'Banjaran, Bandung',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
