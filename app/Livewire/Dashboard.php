<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\AcademicEvent;
use App\Models\Kelas;
use App\Models\PertemuanKelas;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\SchoolSetting;
use App\Models\TeachingSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard - SIAKMAN')]
class Dashboard extends Component {
    public array $stats = [];

    public array $schedule = [];   // guru & murid

    public array $kelasRecap = [];   // admin

    public array $weeklyAttendance = [];  // semua role

    public string $scheduleTitle = 'Jadwal Pembelajaran';

    public function mount(): void {
        $this->loadStats();

        $user = Auth::user();

        match ($user?->role) {
            'guru' => $this->loadGuruSchedule($user),
            'murid' => $this->loadMuridSchedule($user),
            default => $this->loadAdminKelasRecap(),
        };

        $this->loadWeeklyAttendance($user);
    }

    // ── Stats (sama untuk semua role) ─────────────────────────────────────────

    private function loadStats(): void {
        $this->stats = [
            [
                'title' => 'Jumlah Murid',
                'value' => (string) ProfilMurid::count(),
                'icon_bg' => 'bg-blue-100',
                'icon_color' => 'text-blue-600',
                'icon' => 'users',
            ],
            [
                'title' => 'Jumlah Tenaga Pengajar',
                'value' => (string) ProfilGuru::count(),
                'icon_bg' => 'bg-green-100',
                'icon_color' => 'text-green-600',
                'icon' => 'teacher',
            ],
            [
                'title' => 'Acara Mendatang',
                'value' => (string) AcademicEvent::upcoming()->count(),
                'icon_bg' => 'bg-pink-100',
                'icon_color' => 'text-pink-600',
                'icon' => 'calendar',
            ],
            [
                'title' => 'Jumlah Ekstrakurikuler',
                'value' => SchoolSetting::get('jumlah_ekskul', '0'),
                'icon_bg' => 'bg-orange-100',
                'icon_color' => 'text-orange-600',
                'icon' => 'target',
            ],
            [
                'title' => 'Tingkat Akreditasi',
                'value' => SchoolSetting::get('tingkat_akreditasi', '-'),
                'icon_bg' => 'bg-yellow-100',
                'icon_color' => 'text-yellow-600',
                'icon' => 'medal',
            ],
        ];
    }

    // ── Guru: jadwal mengajar berdasarkan guru_id ─────────────────────────────

    private function loadGuruSchedule($user): void {
        $profil = $user->profilGuru;
        if (!$profil) {
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
                'day' => $h,
                'lessons' => $rows[$h]->map(fn ($s) => [
                    'subject' => $s->subject?->subject_name ?? '-',
                    'teacher' => $s->kelas?->nama_kelas ?? '-',   // untuk guru: tampilkan nama kelas
                    'time' => Carbon::parse($s->jam_mulai)->format('H:i')
                                 .' – '
                                 .Carbon::parse($s->jam_selesai)->format('H:i'),
                ])->toArray(),
            ])
            ->values()
            ->toArray();
    }

    // ── Murid: jadwal kelas berdasarkan kelas yang diikuti ────────────────────

    private function loadMuridSchedule($user): void {
        $profil = $user->profilMurid;
        if (!$profil) {
            return;
        }

        $tahunAjaran = $this->currentTahunAjaran();
        $kelas = $profil->kelas()
            ->wherePivot('tahun_ajaran', $tahunAjaran)
            ->first();

        if (!$kelas) {
            // Coba kelas manapun yang diikuti
            $kelas = $profil->kelas()->latest('kelas_murid.id')->first();
        }

        if (!$kelas) {
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
                'day' => $h,
                'lessons' => $rows[$h]->map(fn ($s) => [
                    'subject' => $s->subject?->subject_name ?? '-',
                    'teacher' => $s->guru?->nama_lengkap ?? '-',
                    'time' => Carbon::parse($s->jam_mulai)->format('H:i')
                                 .' – '
                                 .Carbon::parse($s->jam_selesai)->format('H:i'),
                ])->toArray(),
            ])
            ->values()
            ->toArray();
    }

    // ── Admin: rekap semua kelas ──────────────────────────────────────────────

    private function loadAdminKelasRecap(): void {
        // Gunakan tahun ajaran terbaru yang ada di DB agar tidak salah filter
        $tahunAjaran = Kelas::max('tahun_ajaran') ?? $this->currentTahunAjaran();

        // Hitung murid per kelas dalam 1 query (hindari N+1)
        $muridCounts = DB::table('kelas_murid')
            ->where('tahun_ajaran', $tahunAjaran)
            ->selectRaw('kelas_id, count(*) as total')
            ->groupBy('kelas_id')
            ->pluck('total', 'kelas_id');

        $this->kelasRecap = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->with(['waliKelas'])
            ->orderBy('nama_kelas')
            ->get()
            ->map(fn ($k) => [
                'nama' => $k->nama_kelas,
                'kode' => $k->kode_kelas,
                'tahun_ajaran' => $k->tahun_ajaran,
                'wali_kelas' => $k->waliKelas?->nama_lengkap ?? 'Belum ditentukan',
                'jumlah_murid' => $muridCounts[$k->id] ?? 0,
            ])
            ->toArray();
    }

    // ── Tingkat kehadiran mingguan (Senin–Jumat minggu ini) ───────────────────
    // Menggunakan max 2 query untuk seluruh minggu (1 pertemuan + 1 absensi).

    private function loadWeeklyAttendance($user): void {
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $friday = $monday->copy()->addDays(4);
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // ── Query 1: semua pertemuan minggu ini (1 query) ─────────────────────
        $pertemuanQuery = PertemuanKelas::whereBetween('tanggal_pertemuan', [
            $monday->toDateString(),
            $friday->toDateString(),
        ])->select('id', 'tanggal_pertemuan');

        if ($user?->role === 'guru') {
            $guruId = $user->profilGuru?->id;
            if ($guruId) {
                $pertemuanQuery->whereHas(
                    'teachingSchedule',
                    fn ($q) => $q->where('guru_id', $guruId)
                );
            }
        } elseif ($user?->role === 'murid') {
            $kelasId = $user->profilMurid?->kelas()->latest('kelas_murid.id')->first()?->id;
            if ($kelasId) {
                $pertemuanQuery->whereHas(
                    'teachingSchedule',
                    fn ($q) => $q->where('kelas_id', $kelasId)
                );
            }
        }

        $pertemuanRows = $pertemuanQuery->get();
        $allPertemuanIds = $pertemuanRows->pluck('id');

        // Group by date string for easy lookup
        $pertemuanByDate = $pertemuanRows->groupBy(
            fn ($p) => Carbon::parse($p->tanggal_pertemuan)->format('Y-m-d')
        );

        // ── Query 2: semua absensi untuk seluruh pertemuan minggu ini (1 query)
        $absensiRows = collect();
        if ($allPertemuanIds->isNotEmpty()) {
            $absensiQuery = Absensi::whereIn('pertemuan_kelas_id', $allPertemuanIds)
                ->select('pertemuan_kelas_id', 'status_kehadiran');

            if ($user?->role === 'murid' && ($muridId = $user->profilMurid?->id)) {
                $absensiQuery->where('murid_id', $muridId);
            }

            $absensiRows = $absensiQuery->get();
        }

        $absensiByPertemuan = $absensiRows->groupBy('pertemuan_kelas_id');

        // ── Hitung persentase per hari dari data in-memory ────────────────────
        for ($i = 0; $i < 5; $i++) {
            $date = $monday->copy()->addDays($i)->toDateString();
            $ids = $pertemuanByDate->get($date, collect())->pluck('id');

            if ($ids->isEmpty()) {
                $this->weeklyAttendance[] = [
                    'day' => $days[$i],
                    'date' => $date,
                    'percentage' => null,
                    'hadir' => 0,
                    'total' => 0,
                ];

                continue;
            }

            $dayAbsensi = $ids->flatMap(fn ($id) => $absensiByPertemuan->get($id, collect()));
            $total = $dayAbsensi->count();
            $hadir = $dayAbsensi->where('status_kehadiran', 'Hadir')->count();

            $this->weeklyAttendance[] = [
                'day' => $days[$i],
                'date' => $date,
                'percentage' => $total > 0 ? (int) round($hadir / $total * 100) : null,
                'hadir' => $hadir,
                'total' => $total,
            ];
        }
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function currentTahunAjaran(): string {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('m');

        return $month >= 7 ? $year.'/'.($year + 1) : ($year - 1).'/'.$year;
    }

    public function render() {
        return view('livewire.dashboard');
    }
}
