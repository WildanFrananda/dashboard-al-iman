<?php

declare(strict_types=1);

use App\Livewire\ManageClass;
use App\Models\Kelas;
use App\Models\ProfilMurid;
use App\Models\User;

describe('processPromotion', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
        $this->currentYear = '2024/2025';
        $this->nextYear    = '2025/2026';
    });

    it('promotes student from level 5 to level 6 in new academic year', function () {
        $kelas = Kelas::factory()->create(['level' => 5, 'kelompok' => 'A', 'tahun_ajaran' => $this->currentYear]);
        $murid = ProfilMurid::factory()->create();
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $this->currentYear]);

        Livewire\Livewire::test(ManageClass::class)
            ->set('tahun_ajaran', $this->currentYear)
            ->set('newAcademicYear', $this->nextYear)
            ->set('selectedStayBackIds', [])
            ->call('processPromotion');

        $nextKelas = Kelas::where('level', 6)
            ->where('kelompok', 'A')
            ->where('tahun_ajaran', $this->nextYear)
            ->first();

        expect($nextKelas)->not->toBeNull();
        expect($nextKelas->murids()->wherePivot('tahun_ajaran', $this->nextYear)->exists())->toBeTrue();
    });

    it('sets status lulus for level 6 student during promotion', function () {
        $kelas = Kelas::factory()->create(['level' => 6, 'kelompok' => 'A', 'tahun_ajaran' => $this->currentYear]);
        $murid = ProfilMurid::factory()->create(['status' => 'aktif']);
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $this->currentYear]);

        Livewire\Livewire::test(ManageClass::class)
            ->set('tahun_ajaran', $this->currentYear)
            ->set('newAcademicYear', $this->nextYear)
            ->set('selectedStayBackIds', [])
            ->call('processPromotion');

        expect($murid->fresh()->status)->toBe('lulus');
    });

    it('keeps stay-back student at same level in new academic year', function () {
        $kelas = Kelas::factory()->create(['level' => 3, 'kelompok' => 'B', 'tahun_ajaran' => $this->currentYear]);
        $murid = ProfilMurid::factory()->create();
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $this->currentYear]);

        Livewire\Livewire::test(ManageClass::class)
            ->set('tahun_ajaran', $this->currentYear)
            ->set('newAcademicYear', $this->nextYear)
            ->set('selectedStayBackIds', [(string) $murid->id])
            ->call('processPromotion');

        $nextKelas = Kelas::where('level', 3)
            ->where('tahun_ajaran', $this->nextYear)
            ->first();

        expect($nextKelas)->not->toBeNull();
        expect($murid->fresh()->status)->toBe('aktif');
    });

    it('skips student with no class assignment in current year', function () {
        ProfilMurid::factory()->create(['status' => 'aktif']);
        $countBefore = Kelas::count();

        Livewire\Livewire::test(ManageClass::class)
            ->set('tahun_ajaran', $this->currentYear)
            ->set('newAcademicYear', $this->nextYear)
            ->set('selectedStayBackIds', [])
            ->call('processPromotion');

        expect(Kelas::count())->toBe($countBefore);
    });
});
