<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\ProfilGuru;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Lengkapi mata pelajaran yang belum ada ─────────────────────────

        $subjects = $this->seedSubjects();

        // ── 2. Buat guru-guru dengan spesialisasi ─────────────────────────────

        $guru = $this->seedGuru();

        // ── 3. Buat jadwal mengajar per kelas (Senin–Jumat) ──────────────────

        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);
        $kelasList   = Kelas::where('tahun_ajaran', $tahunAjaran)->orderBy('nama_kelas')->get();

        // Bersihkan jadwal lama agar tidak ada duplikat jam/hari
        TeachingSchedule::whereIn('kelas_id', $kelasList->pluck('id'))->delete();

        // Template jadwal mingguan — sama untuk semua kelas
        $jadwalTemplate = [
            [
                'hari'       => 'Senin',
                'jam_mulai'  => '07:30:00',
                'jam_selesai' => '08:45:00',
                'subject_key' => 'pai',
                'guru_key'   => 'agama',
            ],
            [
                'hari'       => 'Senin',
                'jam_mulai'  => '08:45:00',
                'jam_selesai' => '10:00:00',
                'subject_key' => 'mtk',
                'guru_key'   => 'mtk',
            ],
            [
                'hari'       => 'Senin',
                'jam_mulai'  => '10:30:00',
                'jam_selesai' => '11:30:00',
                'subject_key' => 'bind',
                'guru_key'   => 'bind',
            ],
            [
                'hari'       => 'Senin',
                'jam_mulai'  => '11:30:00',
                'jam_selesai' => '12:30:00',
                'subject_key' => 'pkn',
                'guru_key'   => 'pkn',
            ],

            [
                'hari'       => 'Selasa',
                'jam_mulai'  => '07:30:00',
                'jam_selesai' => '08:45:00',
                'subject_key' => 'bind',
                'guru_key'   => 'bind',
            ],
            [
                'hari'       => 'Selasa',
                'jam_mulai'  => '08:45:00',
                'jam_selesai' => '10:00:00',
                'subject_key' => 'mtk',
                'guru_key'   => 'mtk',
            ],
            [
                'hari'       => 'Selasa',
                'jam_mulai'  => '10:30:00',
                'jam_selesai' => '11:30:00',
                'subject_key' => 'ipa',
                'guru_key'   => 'ipa',
            ],
            [
                'hari'       => 'Selasa',
                'jam_mulai'  => '11:30:00',
                'jam_selesai' => '12:30:00',
                'subject_key' => 'ips',
                'guru_key'   => 'ipa',
            ],

            [
                'hari'       => 'Rabu',
                'jam_mulai'  => '07:30:00',
                'jam_selesai' => '08:45:00',
                'subject_key' => 'pjok',
                'guru_key'   => 'pjok',
            ],
            [
                'hari'       => 'Rabu',
                'jam_mulai'  => '08:45:00',
                'jam_selesai' => '10:00:00',
                'subject_key' => 'mtk',
                'guru_key'   => 'mtk',
            ],
            [
                'hari'       => 'Rabu',
                'jam_mulai'  => '10:30:00',
                'jam_selesai' => '11:30:00',
                'subject_key' => 'bind',
                'guru_key'   => 'bind',
            ],
            [
                'hari'       => 'Rabu',
                'jam_mulai'  => '11:30:00',
                'jam_selesai' => '12:30:00',
                'subject_key' => 'ipa',
                'guru_key'   => 'ipa',
            ],

            [
                'hari'       => 'Kamis',
                'jam_mulai'  => '07:30:00',
                'jam_selesai' => '08:45:00',
                'subject_key' => 'snd',
                'guru_key'   => 'pjok',
            ],
            [
                'hari'       => 'Kamis',
                'jam_mulai'  => '08:45:00',
                'jam_selesai' => '10:00:00',
                'subject_key' => 'mtk',
                'guru_key'   => 'mtk',
            ],
            [
                'hari'       => 'Kamis',
                'jam_mulai'  => '10:30:00',
                'jam_selesai' => '11:30:00',
                'subject_key' => 'ips',
                'guru_key'   => 'ipa',
            ],
            [
                'hari'       => 'Kamis',
                'jam_mulai'  => '11:30:00',
                'jam_selesai' => '12:30:00',
                'subject_key' => 'sbdp',
                'guru_key'   => 'pkn',
            ],

            [
                'hari'       => 'Jumat',
                'jam_mulai'  => '07:30:00',
                'jam_selesai' => '08:30:00',
                'subject_key' => 'mtk',
                'guru_key'   => 'mtk',
            ],
            [
                'hari'       => 'Jumat',
                'jam_mulai'  => '08:30:00',
                'jam_selesai' => '09:30:00',
                'subject_key' => 'bind',
                'guru_key'   => 'bind',
            ],
            [
                'hari'       => 'Jumat',
                'jam_mulai'  => '09:30:00',
                'jam_selesai' => '10:30:00',
                'subject_key' => 'pai',
                'guru_key'   => 'agama',
            ],
        ];

        foreach ($kelasList as $kelas) {
            foreach ($jadwalTemplate as $slot) {
                $subjectId = $subjects[$slot['subject_key']]->id ?? null;
                $guruId    = $guru[$slot['guru_key']]->id ?? null;

                if (! $subjectId || ! $guruId) {
                    continue;
                }

                TeachingSchedule::updateOrCreate(
                    [
                        'kelas_id'   => $kelas->id,
                        'subject_id' => $subjectId,
                        'hari'       => $slot['hari'],
                        'jam_mulai'  => $slot['jam_mulai'],
                    ],
                    [
                        'guru_id'    => $guruId,
                        'jam_selesai' => $slot['jam_selesai'],
                    ]
                );
            }
        }

        $this->command->info('SchoolScheduleSeeder: jadwal pembelajaran berhasil dibuat.');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function seedSubjects(): array
    {
        $defs = [
            'mtk'  => ['code' => 'MTK-01', 'name' => 'Matematika',                              'cat' => 'Wajib'],
            'bind' => ['code' => 'BIN-01', 'name' => 'Bahasa Indonesia',                        'cat' => 'Wajib'],
            'ipa'  => ['code' => 'IPA-01', 'name' => 'Ilmu Pengetahuan Alam',                   'cat' => 'Wajib'],
            'ips'  => ['code' => 'IPS-01', 'name' => 'Ilmu Pengetahuan Sosial',                 'cat' => 'Wajib'],
            'pkn'  => ['code' => 'PKN-01', 'name' => 'Pendidikan Kewarganegaraan',              'cat' => 'Wajib'],
            'pai'  => ['code' => 'PAI-01', 'name' => 'Pendidikan Agama Islam',                  'cat' => 'Wajib'],
            'pjok' => ['code' => 'PJK-01', 'name' => 'Pendidikan Jasmani Olahraga Kesehatan',  'cat' => 'Wajib'],
            'sbdp' => ['code' => 'SBP-01', 'name' => 'Seni Budaya dan Prakarya',               'cat' => 'Wajib'],
            'snd'  => ['code' => 'SND-01', 'name' => 'Bahasa Sunda',                           'cat' => 'Muatan Lokal'],
        ];

        $result = [];
        foreach ($defs as $key => $def) {
            $result[$key] = Subject::firstOrCreate(
                ['subject_code' => $def['code']],
                ['subject_name' => $def['name'], 'category' => $def['cat'], 'is_active' => true]
            );
        }

        return $result;
    }

    private function seedGuru(): array
    {
        $defs = [
            'mtk' => [
                'email' => 'guru.mtk@sd-aliman.sch.id',
                'name'  => 'Budi Santoso, S.Pd',
                'nip'   => '197501012000011001',
            ],
            'bind' => [
                'email' => 'guru.bind@sd-aliman.sch.id',
                'name'  => 'Rina Nurdiana, S.Pd',
                'nip'   => '198003152003012002',
            ],
            'ipa' => [
                'email' => 'guru.ipa@sd-aliman.sch.id',
                'name'  => 'Sari Dewi, S.Pd',
                'nip'   => '198505202006012003',
            ],
            'agama' => [
                'email' => 'guru.pai@sd-aliman.sch.id',
                'name'  => 'Ahmad Fauzi, S.Pd.I',
                'nip'   => '197908102004011004',
            ],
            'pjok' => [
                'email' => 'guru.pjok@sd-aliman.sch.id',
                'name'  => 'Deden Supriatna, S.Pd',
                'nip'   => '198612282010011005',
            ],
            'pkn' => [
                'email' => 'guru.pkn@sd-aliman.sch.id',
                'name'  => 'Ratna Sari, S.Pd',
                'nip'   => '199001042012012006',
            ],
        ];

        $result = [];
        foreach ($defs as $key => $def) {
            $user = User::firstOrCreate(
                ['email' => $def['email']],
                [
                    'name'     => $def['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'guru',
                ]
            );

            $result[$key] = ProfilGuru::firstOrCreate(
                ['user_id' => $user->id],
                ['nip' => $def['nip'], 'nama_lengkap' => $def['name']]
            );
        }

        return $result;
    }
}
