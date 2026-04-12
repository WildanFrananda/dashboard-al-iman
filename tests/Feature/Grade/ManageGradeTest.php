<?php

declare(strict_types=1);

use App\Livewire\ManageGrade;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\Subject;
use App\Models\TeachingSchedule;

beforeEach(function () {
    $this->guruData = loginAsGuru();
    $this->guru     = $this->guruData['profil'];
});

describe('ManageGrade — validation', function () {
    it('adds error when nilai exceeds 100', function () {
        Livewire\Livewire::test(ManageGrade::class)
            ->call('updatedGrades', 101, '1.uts')
            ->assertHasErrors(['grades.1.uts']);
    });

    it('adds error when nilai is negative', function () {
        Livewire\Livewire::test(ManageGrade::class)
            ->call('updatedGrades', -1, '1.uts')
            ->assertHasErrors(['grades.1.uts']);
    });

    it('clears error when nilai corrected to valid range', function () {
        Livewire\Livewire::test(ManageGrade::class)
            ->call('updatedGrades', 101, '1.uts')
            ->call('updatedGrades', 85, '1.uts')
            ->assertHasNoErrors(['grades.1.uts']);
    });

    it('accepts boundary nilai of 0', function () {
        Livewire\Livewire::test(ManageGrade::class)
            ->call('updatedGrades', 0, '1.uts')
            ->assertHasNoErrors(['grades.1.uts']);
    });

    it('accepts boundary nilai of 100', function () {
        Livewire\Livewire::test(ManageGrade::class)
            ->call('updatedGrades', 100, '1.uts')
            ->assertHasNoErrors(['grades.1.uts']);
    });
});

describe('ManageGrade — submission', function () {
    it('saves valid grades for students', function () {
        $subject = Subject::factory()->create();
        $kelas   = Kelas::factory()->create();
        $schedule = TeachingSchedule::factory()->create([
            'guru_id'    => $this->guru->id,
            'subject_id' => $subject->id,
            'kelas_id'   => $kelas->id,
        ]);
        $murid = ProfilMurid::factory()->create();
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $schedule->kelas->tahun_ajaran]);

        $tahunAjaran = $schedule->kelas->tahun_ajaran;

        Livewire\Livewire::test(ManageGrade::class)
            ->set('scheduleId', $schedule->id)
            ->set('semester', 1)
            ->set('tahunAjaran', $tahunAjaran)
            ->call('loadStudents')
            ->set("grades.{$murid->id}.uts", 85)
            ->call('submit');

        expect(Nilai::where('murid_id', $murid->id)->where('tipe_nilai', 'UTS')->exists())->toBeTrue();
    });

    it('skips saving when nilai field is empty', function () {
        $subject = Subject::factory()->create();
        $kelas   = Kelas::factory()->create();
        $schedule = TeachingSchedule::factory()->create([
            'guru_id'    => $this->guru->id,
            'subject_id' => $subject->id,
            'kelas_id'   => $kelas->id,
        ]);
        $murid = ProfilMurid::factory()->create();
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $schedule->kelas->tahun_ajaran]);

        Livewire\Livewire::test(ManageGrade::class)
            ->set('scheduleId', $schedule->id)
            ->call('loadStudents')
            ->set("grades.{$murid->id}.uts", null)
            ->call('submit');

        expect(Nilai::count())->toBe(0);
    });
});
