<?php

declare(strict_types=1);

use App\Livewire\ManageClass;
use App\Models\Kelas;

beforeEach(function () {
    loginAsAdmin();
});

describe('ManageClass — CRUD', function () {
    it('shows class list on mount', function () {
        $kelas = Kelas::factory()->create(['nama_kelas' => 'Kelas 1A']);

        Livewire\Livewire::test(ManageClass::class)
            ->assertSee('Kelas 1A');
    });

    it('creates new class with valid data', function () {
        Livewire\Livewire::test(ManageClass::class)
            ->set('kode_kelas', 'SD-1A-TEST')
            ->set('nama_kelas', 'Kelas 1A Test')
            ->set('tahun_ajaran', '2025/2026')
            ->call('save');

        expect(Kelas::where('kode_kelas', 'SD-1A-TEST')->exists())->toBeTrue();
    });

    it('validates kode_kelas is required', function () {
        Livewire\Livewire::test(ManageClass::class)
            ->set('kode_kelas', '')
            ->set('nama_kelas', 'Kelas Test')
            ->set('tahun_ajaran', '2025/2026')
            ->call('save')
            ->assertHasErrors(['kode_kelas']);
    });

    it('validates kode_kelas must be unique', function () {
        Kelas::factory()->create(['kode_kelas' => 'EXISTING-001']);

        Livewire\Livewire::test(ManageClass::class)
            ->set('kode_kelas', 'EXISTING-001')
            ->set('nama_kelas', 'Duplikat Kelas')
            ->set('tahun_ajaran', '2025/2026')
            ->call('save')
            ->assertHasErrors(['kode_kelas']);
    });

    it('updates existing class', function () {
        $kelas = Kelas::factory()->create(['nama_kelas' => 'Old Name']);

        Livewire\Livewire::test(ManageClass::class)
            ->call('edit', $kelas->id)
            ->set('nama_kelas', 'New Name')
            ->call('save');

        expect($kelas->fresh()->nama_kelas)->toBe('New Name');
    });

    it('deletes class', function () {
        $kelas = Kelas::factory()->create();

        Livewire\Livewire::test(ManageClass::class)
            ->call('confirmDelete', $kelas->id)
            ->call('delete', $kelas->id);

        expect(Kelas::find($kelas->id))->toBeNull();
    });
});

describe('ManageClass — promotion modal', function () {
    it('opens promotion modal on openPromotion call', function () {
        Livewire\Livewire::test(ManageClass::class)
            ->call('openPromotion')
            ->assertSet('showPromotionModal', true)
            ->assertSet('promotionStep', 1);
    });

    it('advances to step 2 on startPromotion call', function () {
        Livewire\Livewire::test(ManageClass::class)
            ->call('openPromotion')
            ->set('newAcademicYear', '2025/2026')
            ->call('startPromotion')
            ->assertSet('promotionStep', 2);
    });
});
