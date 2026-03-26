<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KelasSDSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // 1. Pastikan minimal ada 1 Mata Pelajaran Wajib
        $subject = Subject::firstWhere('category', 'Wajib');

        if (!$subject) {
            $subject = Subject::create([
                'subject_code' => 'MTK',
                'subject_name' => 'Matematika Dasar',
                'category' => 'Wajib',
                'is_active' => true,
            ]);
        }

        // 2. Buat Akun Dummy Guru
        $userGuru = User::firstOrCreate([
            'email' => 'guru@sd.com',
        ], [
            'name' => 'Pak Budi Santoso, S.Pd',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        $profilGuru = ProfilGuru::firstOrCreate(
            ['user_id' => $userGuru->id],
            ['nip' => '198001012005011001', 'nama_lengkap' => $userGuru->name]
        );

        // 3. Konfigurasi Kelas SD (1A sampai 6A)
        $tahun_ajaran = date('Y').'/'.(date('Y') + 1);
        $kelasData = [
            ['kode' => 'SD-1A', 'nama' => 'Kelas 1A'],
            ['kode' => 'SD-2A', 'nama' => 'Kelas 2A'],
            ['kode' => 'SD-3A', 'nama' => 'Kelas 3A'],
            ['kode' => 'SD-4A', 'nama' => 'Kelas 4A'],
            ['kode' => 'SD-5A', 'nama' => 'Kelas 5A'],
            ['kode' => 'SD-6A', 'nama' => 'Kelas 6A'],
        ];

        DB::beginTransaction();
        try {
            foreach ($kelasData as $index => $data) {
                // A. Buat Kelas
                $kelas = Kelas::firstOrCreate(
                    ['kode_kelas' => $data['kode']],
                    [
                        'nama_kelas' => $data['nama'],
                        'tahun_ajaran' => $tahun_ajaran,
                        'wali_kelas_id' => $index === 0 ? $profilGuru->id : null,
                    ]
                );

                // B. Buat Jadwal Mengajar untuk Guru (Pak Budi) di setiap kelas
                TeachingSchedule::firstOrCreate([
                    'guru_id' => $profilGuru->id,
                    'subject_id' => $subject->id,
                    'kelas_id' => $kelas->id,
                ], [
                    'hari' => 'Senin',
                    'jam_mulai' => '07:30:00',
                    'jam_selesai' => '09:00:00',
                ]);

                // C. Buat 3 Murid Dummy untuk mengisi kelas tersebut
                for ($i = 1; $i <= 3; $i++) {
                    $emailMurid = 'murid'.strtolower(str_replace('-', '', $data['kode'])).$i.'@sd.com';

                    $userMurid = User::firstOrCreate([
                        'email' => $emailMurid,
                    ], [
                        'name' => 'Siswa '.$i.' '.$data['nama'],
                        'password' => Hash::make('password'),
                        'role' => 'murid',
                    ]);

                    $profilMurid = ProfilMurid::firstOrCreate(
                        ['user_id' => $userMurid->id],
                        ['nis' => str_replace('-', '', $data['kode']).'00'.$i, 'nama_lengkap' => $userMurid->name]
                    );

                    // Hubungkan Murid ke Kelas
                    $kelas->murids()->syncWithoutDetaching([
                        $profilMurid->id => ['tahun_ajaran' => $tahun_ajaran],
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
