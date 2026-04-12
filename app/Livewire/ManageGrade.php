<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Nilai;
use App\Models\TeachingSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Input Nilai - SIAKMAN')]
class ManageGrade extends Component {
    public string $teacherName  = '';
    public string $scheduleId   = '';
    public int    $semester     = 1;
    public string $tahunAjaran  = '';

    /**
     * $grades[murid_id] = ['uts' => int|null, 'uas' => int|null, 'keterangan' => string]
     */
    public array $grades   = [];
    public array $students = [];

    public function mount(): void {
        $user = Auth::user();

        if (! $user || $user->role !== 'guru' || ! $user->profilGuru) {
            abort(403, 'Hanya pengajar yang dapat mengakses halaman ini.');
        }

        $this->teacherName = $user->profilGuru->nama_lengkap;
        $this->tahunAjaran = $this->currentTahunAjaran();
    }

    #[Computed]
    public function schedules() {
        return TeachingSchedule::where('guru_id', Auth::user()->profilGuru->id)
            ->with(['kelas', 'subject'])
            ->get();
    }

    public function updatedScheduleId(): void  { $this->loadStudents(); }
    public function updatedSemester(): void    { $this->loadStudents(); }
    public function updatedTahunAjaran(): void { $this->loadStudents(); }

    /**
     * Dipanggil otomatis setiap kali $grades berubah via wire:model.blur.
     * Validasi range 0–100 dan tampilkan error langsung per field.
     * $key contoh: "3.uts" atau "3.uas"
     */
    public function updatedGrades(mixed $value, string $key): void {
        [$muridId, $field] = array_pad(explode('.', $key, 2), 2, '');

        if (! in_array($field, ['uts', 'uas'], true)) {
            return;
        }

        $errorKey = "grades.{$muridId}.{$field}";

        if ($value === null || $value === '') {
            $this->resetErrorBag($errorKey);
            return;
        }

        $int = (int) $value;

        if ($int < 0 || $int > 100) {
            $this->addError($errorKey, 'Nilai harus antara 0 – 100.');
        } else {
            $this->resetErrorBag($errorKey);
        }
    }

    public function loadStudents(): void {
        $this->students = [];
        $this->grades   = [];

        if (! $this->scheduleId || ! $this->tahunAjaran) {
            return;
        }

        // Security: pastikan jadwal milik guru yang login
        $schedule = TeachingSchedule::where('id', $this->scheduleId)
            ->where('guru_id', Auth::user()->profilGuru->id)
            ->with('kelas.murids')
            ->first();

        if (! $schedule?->kelas) {
            return;
        }

        // Ambil nilai yang sudah ada (UTS & UAS) untuk semester+tahun ini
        $existingNilais = Nilai::where('kelas_id', $schedule->kelas_id)
            ->where('subject_id', $schedule->subject_id)
            ->where('semester', $this->semester)
            ->where('tahun_ajaran', $this->tahunAjaran)
            ->whereIn('murid_id', $schedule->kelas->murids->pluck('id'))
            ->get()
            ->groupBy('murid_id');

        foreach ($schedule->kelas->murids as $murid) {
            $this->students[] = [
                'id'   => $murid->id,
                'name' => $murid->nama_lengkap,
                'nis'  => $murid->nis,
            ];

            $uts = $existingNilais->get($murid->id)?->firstWhere('tipe_nilai', 'UTS');
            $uas = $existingNilais->get($murid->id)?->firstWhere('tipe_nilai', 'UAS');

            $this->grades[$murid->id] = [
                'uts'         => $uts?->nilai,
                'uas'         => $uas?->nilai,
                'keterangan'  => $uts?->keterangan ?? $uas?->keterangan ?? '',
            ];
        }
    }

    public function submit(): void {
        $this->validate([
            'scheduleId'                   => 'required|exists:teaching_schedules,id',
            'semester'                     => 'required|in:1,2',
            'tahunAjaran'                  => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'grades.*.uts'                 => 'nullable|integer|min:0|max:100',
            'grades.*.uas'                 => 'nullable|integer|min:0|max:100',
            'grades.*.keterangan'          => 'nullable|string|max:500',
        ]);

        // Security: verifikasi kepemilikan jadwal
        $schedule = TeachingSchedule::where('id', $this->scheduleId)
            ->where('guru_id', Auth::user()->profilGuru->id)
            ->first();

        if (! $schedule) {
            session()->flash('error', 'Anda tidak memiliki akses ke jadwal ini.');
            return;
        }

        $guruId    = Auth::user()->profilGuru->id;
        $subjectId = $schedule->subject_id;
        $kelasId   = $schedule->kelas_id;

        foreach ($this->students as $student) {
            $muridId    = $student['id'];
            $gradeData  = $this->grades[$muridId] ?? [];
            $keterangan = $gradeData['keterangan'] ?? null;

            foreach (['uts' => 'UTS', 'uas' => 'UAS'] as $key => $tipe) {
                $nilaiInput = $gradeData[$key] ?? null;

                if ($nilaiInput === null || $nilaiInput === '') {
                    continue; // skip jika tidak diisi
                }

                Nilai::updateOrCreate(
                    [
                        'murid_id'     => $muridId,
                        'subject_id'   => $subjectId,
                        'kelas_id'     => $kelasId,
                        'tipe_nilai'   => $tipe,
                        'semester'     => $this->semester,
                        'tahun_ajaran' => $this->tahunAjaran,
                    ],
                    [
                        'guru_id'     => $guruId,
                        'nilai'       => (int) $nilaiInput,
                        'keterangan'  => $keterangan ?: null,
                    ]
                );
            }
        }

        session()->flash('message', 'Nilai berhasil disimpan.');
    }

    private function currentTahunAjaran(): string {
        $year  = (int) now()->format('Y');
        $month = (int) now()->format('m');

        // Tahun ajaran baru mulai Juli
        if ($month >= 7) {
            return $year.'/'.($year + 1);
        }

        return ($year - 1).'/'.$year;
    }

    public function render() {
        return view('livewire.manage-grade');
    }
}
