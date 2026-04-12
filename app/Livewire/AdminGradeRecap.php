<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\ProfilMurid;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rekap Nilai - SIAKMAN')]
class AdminGradeRecap extends Component {
    public string $kelasId     = '';
    public int    $semester    = 1;
    public string $tahunAjaran = '';

    public function mount(): void {
        $user = Auth::user();

        if (! $user || ! in_array($user->role, ['admin', 'guru'], true)) {
            abort(403, 'Hanya admin atau guru yang dapat mengakses halaman ini.');
        }

        $month             = (int) now()->format('m');
        $this->semester    = $month >= 7 ? 1 : 2;
        $this->tahunAjaran = $this->currentTahunAjaran();
    }

    #[Computed]
    public function kelasList() {
        return Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']);
    }

    #[Computed]
    public function rekapData(): array {
        // Ambil daftar murid sesuai filter kelas
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

        // Ambil semua nilai yang relevan dalam 1 query
        $allNilais = Nilai::whereIn('murid_id', $muridList->pluck('id'))
            ->where('semester', $this->semester)
            ->where('tahun_ajaran', $this->tahunAjaran)
            ->when($this->kelasId, fn ($q) => $q->where('kelas_id', $this->kelasId))
            ->with('subject')
            ->get()
            ->groupBy('murid_id');

        $rows = [];
        foreach ($muridList as $murid) {
            $muridNilais = $allNilais->get($murid->id, collect());

            // Group per subject
            $perSubject = $muridNilais->groupBy('subject_id');
            $subjectRows = [];

            foreach ($perSubject as $subjectId => $nilais) {
                $uts = $nilais->firstWhere('tipe_nilai', 'UTS');
                $uas = $nilais->firstWhere('tipe_nilai', 'UAS');

                $subjectRows[] = [
                    'subject' => $nilais->first()->subject?->subject_name ?? '-',
                    'uts'     => $uts?->nilai,
                    'uas'     => $uas?->nilai,
                ];
            }

            $kelas = $murid->kelas->last();

            $rows[] = [
                'nis'        => $murid->nis,
                'nama'       => $murid->nama_lengkap,
                'kelas'      => $kelas?->nama_kelas ?? '-',
                'subjects'   => $subjectRows,
                'has_nilai'  => $muridNilais->isNotEmpty(),
            ];
        }

        return $rows;
    }

    private function currentTahunAjaran(): string {
        $year  = (int) now()->format('Y');
        $month = (int) now()->format('m');

        return $month >= 7 ? $year.'/'.($year + 1) : ($year - 1).'/'.$year;
    }

    public function render() {
        return view('livewire.admin-grade-recap');
    }
}
