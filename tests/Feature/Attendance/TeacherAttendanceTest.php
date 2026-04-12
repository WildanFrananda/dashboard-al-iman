<?php

declare(strict_types=1);

use App\Livewire\TeacherAttendance;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\PertemuanKelas;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\Subject;
use App\Models\TeachingSchedule;

describe('TeacherAttendance', function () {
    beforeEach(function () {
        $this->guruData = loginAsGuru();
        $this->guru     = $this->guruData['profil'];
    });

    it('mounts without error for guru', function () {
        Livewire\Livewire::test(TeacherAttendance::class)->assertOk();
    });

    it('loads students when valid schedule and date are provided', function () {
        $kelas   = Kelas::factory()->create(['tahun_ajaran' => date('Y') . '/' . (date('Y') + 1)]);
        $subject = Subject::factory()->create();
        $schedule = TeachingSchedule::factory()->create([
            'guru_id'    => $this->guru->id,
            'subject_id' => $subject->id,
            'kelas_id'   => $kelas->id,
        ]);
        $murid = ProfilMurid::factory()->create();
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $kelas->tahun_ajaran]);

        Livewire\Livewire::test(TeacherAttendance::class)
            ->set('scheduleId', $schedule->id)
            ->set('date', now()->format('Y-m-d'))
            ->call('loadStudents')
            ->assertSet('students', fn ($students) => count($students) >= 1);
    });

    it('saves absensi records on submit', function () {
        $kelas   = Kelas::factory()->create(['tahun_ajaran' => date('Y') . '/' . (date('Y') + 1)]);
        $subject = Subject::factory()->create();
        $schedule = TeachingSchedule::factory()->create([
            'guru_id'    => $this->guru->id,
            'subject_id' => $subject->id,
            'kelas_id'   => $kelas->id,
        ]);
        $murid = ProfilMurid::factory()->create();
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $kelas->tahun_ajaran]);

        Livewire\Livewire::test(TeacherAttendance::class)
            ->set('scheduleId', $schedule->id)
            ->set('date', now()->format('Y-m-d'))
            ->call('loadStudents')
            ->set("attendances.{$murid->id}", 'Hadir')
            ->set('materi', 'Bab 1 Pengenalan')
            ->call('submit');

        expect(Absensi::where('murid_id', $murid->id)->exists())->toBeTrue();
    });
});
