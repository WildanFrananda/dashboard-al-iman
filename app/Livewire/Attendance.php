<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Absensi - SIAKMAN')]
class Attendance extends Component {
    public array $summary = [];

    public array $attendanceRecords = [];

    private const STATUS_MAP = [
        'Hadir' => 'H',
        'Izin'  => 'I',
        'Sakit' => 'S',
        'Alpa'  => 'A',
    ];

    public function mount(): void {
        $user  = Auth::user();
        $murid = $user?->profilMurid;

        if (! $murid) {
            $this->summary          = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];
            $this->attendanceRecords = [];

            return;
        }

        $absensiList = Absensi::where('murid_id', $murid->id)
            ->with([
                'pertemuanKelas' => fn ($q) => $q->orderBy('tanggal_pertemuan'),
                'pertemuanKelas.teachingSchedule.subject',
            ])
            ->get()
            ->sortBy('pertemuanKelas.tanggal_pertemuan');

        // --- Summary cards ---
        $this->summary = [
            'hadir' => $absensiList->where('status_kehadiran', 'Hadir')->count(),
            'izin'  => $absensiList->where('status_kehadiran', 'Izin')->count(),
            'sakit' => $absensiList->where('status_kehadiran', 'Sakit')->count(),
            'alfa'  => $absensiList->where('status_kehadiran', 'Alpa')->count(),
        ];

        // --- Group by subject & build history badges ---
        $grouped = $absensiList->groupBy(
            fn ($a) => optional(
                optional($a->pertemuanKelas)->teachingSchedule?->subject
            )->subject_name ?? 'Tidak Diketahui'
        );

        $records = [];
        foreach ($grouped as $subjectName => $items) {
            $history    = [];
            $meetNumber = 1;

            foreach ($items as $absensi) {
                $history[] = [
                    'meet'    => $meetNumber++,
                    'status'  => self::STATUS_MAP[$absensi->status_kehadiran] ?? '?',
                    'tanggal' => optional($absensi->pertemuanKelas)->tanggal_pertemuan,
                ];
            }

            $records[] = [
                'subject' => $subjectName,
                'history' => $history,
            ];
        }

        $this->attendanceRecords = $records;
    }

    public function render() {
        return view('livewire.attendance');
    }
}
