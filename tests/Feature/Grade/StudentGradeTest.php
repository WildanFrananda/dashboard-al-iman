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

    // Nilai untuk murid ini
    Nilai::factory()->create([
        'murid_id'    => $this->murid->id,
        'subject_id'  => $subject->id,
        'guru_id'     => $guru->id,
        'kelas_id'    => $kelas->id,
        'tipe_nilai'  => 'UTS',
        'nilai'       => 90,
        'semester'    => 1,
        'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1),
    ]);

    Livewire\Livewire::test(StudentGrade::class)
        ->assertSee('Matematika');
});

it('mounts without error for student with no grades', function () {
    Livewire\Livewire::test(StudentGrade::class)
        ->assertOk();
});
