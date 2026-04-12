<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\ProfilMurid;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rekap Absensi - SIAKMAN')]
class AttendanceRecap extends Component {
    public int    $bulan   = 0;
    public int    $tahun   = 0;
    public string $kelasId = '';

    public function mount(): void {
        $user = Auth::user();

        if (! $user || ! in_array($user->role, ['guru', 'admin'], true)) {
            redirect()->route('dashboard');
            return;
        }

        $this->bulan = (int) now()->format('m');
        $this->tahun = (int) now()->format('Y');
    }

    #[Computed]
    public function kelasList() {
        return Kelas::orderBy('nama_kelas')->get();
    }

    #[Computed]
    public function rekapData(): array {
        // Query murid — filter kelas jika dipilih
        $muridQuery = ProfilMurid::query()->with('kelas');

        if ($this->kelasId) {
            $muridQuery->whereHas(
                'kelas',
                fn ($q) => $q->where('kelas.id', $this->kelasId)
            );
        }

        $muridList = $muridQuery->orderBy('nama_lengkap')->get();

        if ($muridList->isEmpty()) {
            return [];
        }

        // Ambil semua absensi sekaligus (1 query) lalu group di PHP
        $allAbsensi = Absensi::whereIn('murid_id', $muridList->pluck('id'))
            ->whereHas(
                'pertemuanKelas',
                fn ($q) => $q->whereYear('tanggal_pertemuan', $this->tahun)
                             ->whereMonth('tanggal_pertemuan', $this->bulan)
            )
            ->with('pertemuanKelas')
            ->get()
            ->groupBy('murid_id');

        $rows = [];
        foreach ($muridList as $murid) {
            $absensiMurid = $allAbsensi->get($murid->id, collect());

            $hadir = $absensiMurid->where('status_kehadiran', 'Hadir')->count();
            $izin  = $absensiMurid->where('status_kehadiran', 'Izin')->count();
            $sakit = $absensiMurid->where('status_kehadiran', 'Sakit')->count();
            $alpa  = $absensiMurid->where('status_kehadiran', 'Alpa')->count();
            $total = $absensiMurid->count();

            $persentase = $total > 0
                ? round(($hadir / $total) * 100, 1)
                : null;

            // Ambil nama kelas aktif (pivot latest)
            $namaKelas = $murid->kelas->last()?->nama_kelas ?? '-';

            $rows[] = [
                'nis'         => $murid->nis,
                'nama'        => $murid->nama_lengkap,
                'kelas'       => $namaKelas,
                'hadir'       => $hadir,
                'izin'        => $izin,
                'sakit'       => $sakit,
                'alpa'        => $alpa,
                'total'       => $total,
                'persentase'  => $persentase,
            ];
        }

        return $rows;
    }

    public function render() {
        return view('livewire.attendance-recap');
    }
}
