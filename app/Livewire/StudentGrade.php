<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Nilai;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Nilai Saya - SIAKMAN')]
class StudentGrade extends Component {
    public int $semester = 1;

    public string $tahunAjaran = '';

    public function mount(): void {
        $user = Auth::user();

        if (!$user?->profilMurid) {
            abort(403, 'Halaman ini hanya untuk murid.');
        }

        // Default ke semester yang aktif berdasarkan bulan
        $month = (int) now()->format('m');
        $this->semester = $month >= 7 ? 1 : 2;

        $this->tahunAjaran = $this->currentTahunAjaran();
    }

    #[Computed]
    public function gradeRecords(): array {
        $murid = Auth::user()->profilMurid;

        if (!$murid) {
            return [];
        }

        $nilais = Nilai::where('murid_id', $murid->id)
            ->where('semester', $this->semester)
            ->where('tahun_ajaran', $this->tahunAjaran)
            ->with('subject')
            ->orderBy('subject_id')
            ->get();

        // Group by subject → satu baris per mata pelajaran dengan kolom UTS + UAS
        $grouped = $nilais->groupBy('subject_id');

        $records = [];
        foreach ($grouped as $subjectNilais) {
            $uts = $subjectNilais->firstWhere('tipe_nilai', 'UTS');
            $uas = $subjectNilais->firstWhere('tipe_nilai', 'UAS');

            $records[] = [
                'subject' => $subjectNilais->first()->subject?->subject_name ?? '-',
                'uts' => $uts?->nilai,
                'uas' => $uas?->nilai,
                'keterangan' => $uts?->keterangan ?? $uas?->keterangan ?? null,
            ];
        }

        return $records;
    }

    #[Computed]
    public function summary(): array {
        $records = $this->gradeRecords;

        $utsValues = array_filter(array_column($records, 'uts'), fn ($v) => $v !== null);
        $uasValues = array_filter(array_column($records, 'uas'), fn ($v) => $v !== null);

        return [
            'rata_uts' => count($utsValues) > 0 ? round(array_sum($utsValues) / count($utsValues), 1) : null,
            'rata_uas' => count($uasValues) > 0 ? round(array_sum($uasValues) / count($uasValues), 1) : null,
            'mapel' => count($records),
        ];
    }

    private function currentTahunAjaran(): string {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('m');

        return $month >= 7 ? $year.'/'.($year + 1) : ($year - 1).'/'.$year;
    }

    public function render() {
        return view('livewire.student-grade');
    }
}
