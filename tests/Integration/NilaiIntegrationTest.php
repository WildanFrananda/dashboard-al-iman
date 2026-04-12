<?php

declare(strict_types=1);

use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\Subject;

describe('Nilai unique constraint', function () {
    it('saves first nilai record without error', function () {
        $nilai = Nilai::factory()->create(['tipe_nilai' => 'UTS', 'semester' => 1]);
        expect(Nilai::count())->toBe(1);
        expect($nilai->nilai)->toBeValidNilai();
    });

    it('throws exception when inserting duplicate nilai record', function () {
        $murid   = ProfilMurid::factory()->create();
        $subject = Subject::factory()->create();
        $guru    = ProfilGuru::factory()->create();
        $kelas   = Kelas::factory()->create();

        $shared = [
            'murid_id'    => $murid->id,
            'subject_id'  => $subject->id,
            'guru_id'     => $guru->id,
            'kelas_id'    => $kelas->id,
            'tipe_nilai'  => 'UTS',
            'semester'    => 1,
            'tahun_ajaran' => '2025/2026',
        ];

        Nilai::create(array_merge($shared, ['nilai' => 80]));

        expect(fn () => Nilai::create(array_merge($shared, ['nilai' => 90])))
            ->toThrow(\Illuminate\Database\QueryException::class);
    });

    it('updateOrCreate on same unique key keeps record count at 1', function () {
        $murid   = ProfilMurid::factory()->create();
        $subject = Subject::factory()->create();
        $guru    = ProfilGuru::factory()->create();
        $kelas   = Kelas::factory()->create();

        $keys = [
            'murid_id'    => $murid->id,
            'subject_id'  => $subject->id,
            'guru_id'     => $guru->id,
            'kelas_id'    => $kelas->id,
            'tipe_nilai'  => 'UTS',
            'semester'    => 1,
            'tahun_ajaran' => '2025/2026',
        ];

        Nilai::updateOrCreate($keys, ['nilai' => 75]);
        Nilai::updateOrCreate($keys, ['nilai' => 88]);

        expect(Nilai::count())->toBe(1);
        expect(Nilai::first()->nilai)->toBe(88);
    });
});
