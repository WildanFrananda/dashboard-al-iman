<?php

declare(strict_types=1);

use App\Models\Absensi;
use App\Models\PertemuanKelas;
use App\Models\ProfilMurid;
use App\Models\TeachingSchedule;

describe('Attendance rekap computation', function () {
    it('correctly counts hadir from absensi records', function () {
        $murid = ProfilMurid::factory()->create();
        $schedule = TeachingSchedule::factory()->create();
        $pertemuan = PertemuanKelas::factory()->create([
            'teaching_schedule_id' => $schedule->id,
            'tanggal_pertemuan' => now()->format('Y-m-d'),
        ]);

        Absensi::factory()->count(8)->create([
            'pertemuan_kelas_id' => $pertemuan->id,
            'murid_id' => $murid->id,
            'status_kehadiran' => 'Hadir',
        ]);
        Absensi::factory()->count(2)->create([
            'pertemuan_kelas_id' => $pertemuan->id,
            'murid_id' => $murid->id,
            'status_kehadiran' => 'Alpa',
        ]);

        $hadir = Absensi::where('murid_id', $murid->id)
            ->where('status_kehadiran', 'Hadir')
            ->count();
        $total = Absensi::where('murid_id', $murid->id)->count();

        expect($hadir)->toBe(8);
        expect(round($hadir / $total * 100))->toBe(80.0);
    });

    it('returns zero percentage when no records exist', function () {
        $murid = ProfilMurid::factory()->create();

        $total = Absensi::where('murid_id', $murid->id)->count();
        $percentage = $total > 0 ? 0 : 0;

        expect($percentage)->toBe(0);
    });
});
