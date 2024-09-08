<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Sesuaikan email dengan email admin yang diinginkan
            [
                'name' => 'Admin',
                'password' => Hash::make('123'), // Sesuaikan password dengan yang diinginkan
                'role' => 'admin',
            ]
        );
    }
}
