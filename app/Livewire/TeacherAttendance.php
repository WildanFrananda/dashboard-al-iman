<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\PertemuanKelas;
use App\Models\TeachingSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Absensi Pengajar - SIAKMAN')]
class TeacherAttendance extends Component {
    public string $teacherName = '';

    public string $scheduleId = '';

    public string $date = '';

    public array $students = [];

    public array $attendances = [];

    #[Computed]
    public function schedules() {
        $user = Auth::user();

        return TeachingSchedule::where('guru_id', $user->profilGuru->id)
            ->with(['kelas', 'subject'])
            ->get();
    }

    public function mount(): void {
        $user = Auth::user();

        if (!$user || $user->role !== 'guru' || !$user->profilGuru) {
            abort(403, 'Hanya pengajar yang dapat mengakses halaman ini.');
        }

        $this->teacherName = $user->profilGuru->nama_lengkap;
        $this->date = date('d/m/Y');
    }

    // Otomatis dipanggil saat scheduleId berubah (wire:model.live)
    public function updatedScheduleId(): void {
        $this->loadStudents();
    }

    // Otomatis dipanggil saat date berubah (wire:model.live.debounce)
    public function updatedDate(): void {
        $this->loadStudents();
    }

    public function loadStudents(): void {
        $this->students = [];
        $this->attendances = [];

        if (!$this->scheduleId || !$this->date) {
            return;
        }

        $schedule = TeachingSchedule::with('kelas.murids')->find($this->scheduleId);

        if (!$schedule || !$schedule->kelas) {
            return;
        }

        $dateParts = explode('/', $this->date);
        if (count($dateParts) !== 3) {
            return;
        }

        $formattedDate = $dateParts[2].'-'.$dateParts[1].'-'.$dateParts[0];

        $pertemuan = PertemuanKelas::where('teaching_schedule_id', $schedule->id)
            ->where('tanggal_pertemuan', $formattedDate)
            ->first();

        $existingAbsensi = [];
        if ($pertemuan) {
            $existingAbsensi = Absensi::where('pertemuan_kelas_id', $pertemuan->id)
                ->pluck('status_kehadiran', 'murid_id')
                ->toArray();
        }

        foreach ($schedule->kelas->murids as $murid) {
            $this->students[] = [
                'id' => $murid->id,
                'name' => $murid->nama_lengkap,
            ];

            // KEY = murid id (bukan index) supaya $set dari blade tepat sasaran
            $this->attendances[$murid->id] = $existingAbsensi[$murid->id] ?? 'Hadir';
        }
    }

    public function submit(): void {
        $this->validate([
            'scheduleId' => 'required',
            'date' => 'required',
            'attendances.*' => 'required|in:Hadir,Izin,Sakit,Alfa',
        ]);

        $dateParts = explode('/', $this->date);
        if (count($dateParts) !== 3) {
            session()->flash('error', 'Format tanggal tidak valid (gunakan dd/mm/yyyy).');

            return;
        }

        $formattedDate = $dateParts[2].'-'.$dateParts[1].'-'.$dateParts[0];

        $pertemuan = PertemuanKelas::firstOrCreate(
            [
                'teaching_schedule_id' => $this->scheduleId,
                'tanggal_pertemuan' => $formattedDate,
            ],
            ['materi' => 'Pertemuan Reguler']
        );

        foreach ($this->students as $student) {
            $studentId = $student['id'];
            $statusInput = $this->attendances[$studentId] ?? 'Hadir';

            // Normalisasi: "Alfa" di UI disimpan sebagai "Alpa" di DB
            $dbStatus = $statusInput === 'Alfa' ? 'Alpa' : $statusInput;

            Absensi::updateOrCreate(
                [
                    'pertemuan_kelas_id' => $pertemuan->id,
                    'murid_id' => $studentId,
                ],
                [
                    'status_kehadiran' => $dbStatus,
                    'waktu_absen' => now(),
                ]
            );
        }

        session()->flash('message', 'Data absensi berhasil disimpan.');
    }

    public function render() {
        return view('livewire.teacher-attendance');
    }
}
