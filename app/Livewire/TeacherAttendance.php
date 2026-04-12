<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\PertemuanKelas;
use App\Models\TeachingSchedule;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
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

    /** Format: Y-m-d (value dari date input type="date") */
    public string $date = '';

    public string $materi = '';

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
        $this->date = now()->format('Y-m-d');
    }

    public function updatedScheduleId(): void {
        $this->loadStudents();
    }

    public function updatedDate(): void {
        $this->loadStudents();
    }

    public function loadStudents(): void {
        $this->students = [];
        $this->attendances = [];

        if (!$this->scheduleId || !$this->date) {
            return;
        }

        // Security: pastikan jadwal milik guru yang sedang login
        $schedule = TeachingSchedule::where('id', $this->scheduleId)
            ->where('guru_id', Auth::user()->profilGuru->id)
            ->with('kelas.murids')
            ->first();

        if (!$schedule || !$schedule->kelas) {
            return;
        }

        $pertemuan = PertemuanKelas::where('teaching_schedule_id', $schedule->id)
            ->where('tanggal_pertemuan', $this->date)
            ->first();

        $existingAbsensi = [];
        if ($pertemuan) {
            $existingAbsensi = Absensi::where('pertemuan_kelas_id', $pertemuan->id)
                ->pluck('status_kehadiran', 'murid_id')
                ->toArray();
            $this->materi = $pertemuan->materi ?? '';
        }

        foreach ($schedule->kelas->murids as $murid) {
            $this->students[] = [
                'id' => $murid->id,
                'name' => $murid->nama_lengkap,
            ];

            $this->attendances[$murid->id] = $existingAbsensi[$murid->id] ?? 'Hadir';
        }
    }

    public function submit(): void {
        $this->validate([
            'scheduleId' => 'required|exists:teaching_schedules,id',
            'date' => 'required|date_format:Y-m-d',
            'materi' => 'nullable|string|max:500',
            'attendances.*' => 'required|in:Hadir,Izin,Sakit,Alpa',
        ]);

        // Security: verifikasi kepemilikan jadwal
        $schedule = TeachingSchedule::where('id', $this->scheduleId)
            ->where('guru_id', Auth::user()->profilGuru->id)
            ->first();

        if (!$schedule) {
            session()->flash('error', 'Anda tidak memiliki akses ke jadwal ini.');

            return;
        }

        try {
            $formattedDate = Carbon::createFromFormat('Y-m-d', $this->date)->format('Y-m-d');
        } catch (InvalidFormatException) {
            session()->flash('error', 'Format tanggal tidak valid.');

            return;
        }

        $pertemuan = PertemuanKelas::firstOrCreate(
            [
                'teaching_schedule_id' => $this->scheduleId,
                'tanggal_pertemuan' => $formattedDate,
            ],
            ['materi' => $this->materi ?: 'Pertemuan Reguler']
        );

        // Jika pertemuan sudah ada, update materi jika diisi
        if (!$pertemuan->wasRecentlyCreated && $this->materi) {
            $pertemuan->update(['materi' => $this->materi]);
        }

        foreach ($this->students as $student) {
            $studentId = $student['id'];
            $dbStatus = $this->attendances[$studentId] ?? 'Hadir';

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
