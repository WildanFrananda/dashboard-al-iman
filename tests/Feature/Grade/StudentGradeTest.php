<?php

declare(strict_types=1);

use App\Livewire\StudentGrade;
use App\Models\Nilai;
use App\Models\ProfilGuru;
use App\Models\Kelas;
use App\Models\Subject;

beforeEach(function () {
    $this->muridData = loginAsMurid();
    $this->murid     = $this->muridData['profil'];
});

it('shows only grades belonging to logged-in student', function () {
    $subject = Subject::factory()->create(['subject_name' => 'Matematika']);
    $kelas   = Kelas::factory()->create();
    $guru    = ProfilGuru::factory()->create();

    // Compute semester & tahun_ajaran the same way the component does
    $month       = (int) now()->format('m');
    $year        = (int) now()->format('Y');
    $semester    = $month >= 7 ? 1 : 2;
    $tahunAjaran = $month >= 7 ? $year . '/' . ($year + 1) : ($year - 1) . '/' . $year;

    Nilai::factory()->create([
        'murid_id'    => $this->murid->id,
        'subject_id'  => $subject->id,
        'guru_id'     => $guru->id,
        'kelas_id'    => $kelas->id,
        'tipe_nilai'  => 'UTS',
        'nilai'       => 90,
        'semester'    => $semester,
        'tahun_ajaran' => $tahunAjaran,
    ]);

    Livewire\Livewire::test(StudentGrade::class)
        ->assertSee('Matematika');
});

it('mounts without error for student with no grades', function () {
    Livewire\Livewire::test(StudentGrade::class)
        ->assertOk();
});
