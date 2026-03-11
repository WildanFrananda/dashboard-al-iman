<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // 1. Create Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@email.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Create Guru
        $guruUser = User::create([
            'name' => 'Budi Santoso (Guru)',
            'email' => 'guru@email.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        ProfilGuru::create([
            'user_id' => $guruUser->id,
            'nip' => '198001012005011000',
            'nama_lengkap' => 'Budi Santoso, S.Pd',
        ]);

        // 3. Create Murid
        $muridUser = User::create([
            'name' => 'Chika Karena',
            'email' => 'murid@email.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        ProfilMurid::create([
            'user_id' => $muridUser->id,
            'nis' => '20230001',
            'nama_lengkap' => 'Chika Karena',
        ]);

        $muridUser2 = User::create([
            'name' => 'Siti Aisyah',
            'email' => 'murid2@email.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        ProfilMurid::create([
            'user_id' => $muridUser2->id,
            'nis' => '20230002',
            'nama_lengkap' => 'Siti Aisyah',
        ]);
    }
}
