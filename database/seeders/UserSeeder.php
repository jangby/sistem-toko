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
    User::create([
        'name' => 'Owner Toko',
        'email' => 'admin@toko.com',
        'password' => Hash::make('password123'), // Ganti password sesuai keinginan
        'role' => 'admin',
    ]);

    // Opsional: Buat 1 akun kasir untuk tes nanti
    User::create([
        'name' => 'Kasir 1',
        'email' => 'kasir@toko.com',
        'password' => Hash::make('password123'),
        'role' => 'kasir',
    ]);
}
}
