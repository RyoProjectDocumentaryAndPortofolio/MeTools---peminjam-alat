<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
           [
                'name' => 'Bagus Karim',
                'email' => 'bagus@gmail.com',
                'password' => Hash::make('password123'),
                'role'  => 'admin',
                'no_hp' => '081234567890',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'role'  => 'admin',
                'no_hp' => '081234567890',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'petugas',
                'email' => 'petugas@gmail.com',
                'password' => Hash::make('petugas'),
                'role'  => 'petugas',
                'no_hp' => '081234567890',
                'alamat' => 'Bandung, West Java',
            ],
             [
                'name' => 'peminjam',
                'email' => 'peminjam@gmail.com',
                'password' => Hash::make('peminjam'),
                'role' => 'peminjam',
                'no_hp' => '084567890123',
                'alamat' => 'Dayeuhkolot, Bandung',
            ],
            [
                'name' => 'Arif Muhammad',
                'email' => 'arif@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'no_hp' => '082345678901',
                'alamat' => 'Baleendah, Bandung',
            ],
            [
                'name' => 'Rian Setiawan',
                'email' => 'rian@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '083456789012',
                'alamat' => 'Ciparay, Bandung',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '084567890123',
                'alamat' => 'Dayeuhkolot, Bandung',
            ],
            [
                'name' => 'Eka Pratama',
                'email' => 'eka@gmail.com',
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