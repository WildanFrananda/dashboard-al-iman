<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Support\RaportBuilder;
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
    public function studentInfo(): array {
        return app(RaportBuilder::class)->studentInfo(Auth::user()->profilMurid, $this->tahunAjaran);
    }

    #[Computed]
    public function gradeRecords(): array {
        $murid = Auth::user()->profilMurid;

        if (!$murid) {
            return [];
        }

        return app(RaportBuilder::class)->records($murid, $this->semester, $this->tahunAjaran);
    }

    #[Computed]
    public function summary(): array {
        return app(RaportBuilder::class)->summary($this->gradeRecords);
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
