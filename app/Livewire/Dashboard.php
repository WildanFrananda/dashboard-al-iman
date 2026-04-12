<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AcademicEvent;
use App\Models\Kelas;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\SchoolSetting;
use App\Models\TeachingSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard - SIAKMAN')]
class Dashboard extends Component {
    public array  $stats      = [];
    public array  $schedule   = [];   // guru & murid
    public array  $kelasRecap = [];   // admin
    public string $scheduleTitle = 'Jadwal Pembelajaran';

    public function mount(): void {
        $this->loadStats();

        $user = Auth::user();

        match ($user?->role) {
            'guru'  => $this->loadGuruSchedule($user),
            'murid' => $this->loadMuridSchedule($user),
            default => $this->loadAdminKelasRecap(),
        };
    }

    // ── Stats (sama untuk semua role) ─────────────────────────────────────────

    private function loadStats(): void {
        $this->stats = [
            [
                'title'      => 'Jumlah Murid',
                'value'      => (string) ProfilMurid::count(),
                'icon_bg'    => 'bg-blue-100',
                'icon_color' => 'text-blue-600',
                'icon'       => 'users',
            ],
            [
                'title'      => 'Jumlah Tenaga Pengajar',
                'value'      => (string) ProfilGuru::count(),
                'icon_bg'    => 'bg-green-100',
                'icon_color' => 'text-green-600',
                'icon'       => 'teacher',
            ],
            [
                'title'      => 'Acara Mendatang',
                'value'      => (string) AcademicEvent::upcoming()->count(),
                'icon_bg'    => 'bg-pink-100',
                'icon_color' => 'text-pink-600',
                'icon'       => 'calendar',
            ],
            [
                'title'      => 'Jumlah Ekstrakurikuler',
                'value'      => SchoolSetting::get('jumlah_ekskul', '0'),
                'icon_bg'    => 'bg-orange-100',
                'icon_color' => 'text-orange-600',
                'icon'       => 'target',
            ],
            [
                'title'      => 'Tingkat Akreditasi',
                'value'      => SchoolSetting::get('tingkat_akreditasi', '-'),
                'icon_bg'    => 'bg-yellow-100',
                'icon_color' => 'text-yellow-600',
                'icon'       => 'medal',
            ],
        ];
    }

    // ── Guru: jadwal mengajar berdasarkan guru_id ─────────────────────────────

    private function loadGuruSchedule($user): void {
        $profil = $user->profilGuru;
        if (! $profil) {
            return;
        }

        $this->scheduleTitle = 'Jadwal Mengajar Saya';

        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $rows = TeachingSchedule::where('guru_id', $profil->id)
            ->with(['subject', 'kelas'])
            ->orderByRaw("CASE hari
                WHEN 'Senin'  THEN 1
                WHEN 'Selasa' THEN 2
                WHEN 'Rabu'   THEN 3
                WHEN 'Kamis'  THEN 4
                WHEN 'Jumat'  THEN 5
                ELSE 6 END")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        $this->schedule = collect($hariOrder)
            ->filter(fn ($h) => $rows->has($h))
            ->map(fn ($h) => [
                'day'     => $h,
                'lessons' => $rows[$h]->map(fn ($s) => [
                    'subject' => $s->subject?->subject_name ?? '-',
                    'teacher' => $s->kelas?->nama_kelas ?? '-',   // untuk guru: tampilkan nama kelas
                    'time'    => \Carbon\Carbon::parse($s->jam_mulai)->format('H:i')
                                 . ' – '
                                 . \Carbon\Carbon::parse($s->jam_selesai)->format('H:i'),
                ])->toArray(),
            ])
            ->values()
            ->toArray();
    }

    // ── Murid: jadwal kelas berdasarkan kelas yang diikuti ────────────────────

    private function loadMuridSchedule($user): void {
        $profil = $user->profilMurid;
        if (! $profil) {
            return;
        }

        $tahunAjaran = $this->currentTahunAjaran();
        $kelas = $profil->kelas()
            ->wherePivot('tahun_ajaran', $tahunAjaran)
            ->first();

        if (! $kelas) {
            // Coba kelas manapun yang diikuti
            $kelas = $profil->kelas()->latest('kelas_murid.id')->first();
        }

        if (! $kelas) {
            return;
        }

        $this->scheduleTitle = "Jadwal Kelas – {$kelas->nama_kelas}";

        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $rows = TeachingSchedule::where('kelas_id', $kelas->id)
            ->with(['subject', 'guru'])
            ->orderByRaw("CASE hari
                WHEN 'Senin'  THEN 1
                WHEN 'Selasa' THEN 2
                WHEN 'Rabu'   THEN 3
                WHEN 'Kamis'  THEN 4
                WHEN 'Jumat'  THEN 5
                ELSE 6 END")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        $this->schedule = collect($hariOrder)
            ->filter(fn ($h) => $rows->has($h))
            ->map(fn ($h) => [
                'day'     => $h,
                'lessons' => $rows[$h]->map(fn ($s) => [
                    'subject' => $s->subject?->subject_name ?? '-',
                    'teacher' => $s->guru?->nama_lengkap ?? '-',
                    'time'    => \Carbon\Carbon::parse($s->jam_mulai)->format('H:i')
                                 . ' – '
                                 . \Carbon\Carbon::parse($s->jam_selesai)->format('H:i'),
                ])->toArray(),
            ])
            ->values()
            ->toArray();
    }

    // ── Admin: rekap semua kelas ──────────────────────────────────────────────

    private function loadAdminKelasRecap(): void {
        // Gunakan tahun ajaran terbaru yang ada di DB agar tidak salah filter
        $tahunAjaran = Kelas::max('tahun_ajaran') ?? $this->currentTahunAjaran();

        $this->kelasRecap = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->with(['waliKelas', 'murids'])
            ->orderBy('nama_kelas')
            ->get()
            ->map(fn ($k) => [
                'nama'         => $k->nama_kelas,
                'kode'         => $k->kode_kelas,
                'tahun_ajaran' => $k->tahun_ajaran,
                'wali_kelas'   => $k->waliKelas?->nama_lengkap ?? 'Belum ditentukan',
                'jumlah_murid' => $k->murids()->wherePivot('tahun_ajaran', $tahunAjaran)->count(),
            ])
            ->toArray();
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function currentTahunAjaran(): string {
        $year  = (int) now()->format('Y');
        $month = (int) now()->format('m');

        return $month >= 7 ? $year . '/' . ($year + 1) : ($year - 1) . '/' . $year;
    }

    public function render() {
        return view('livewire.dashboard');
    }
}
