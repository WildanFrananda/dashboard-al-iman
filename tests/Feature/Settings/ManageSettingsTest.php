<?php

declare(strict_types=1);

use App\Livewire\ManageSettings;
use App\Models\SchoolSetting;

describe('ManageSettings', function () {
    it('blocks non-admin from mounting', function () {
        loginAsGuru();
        $this->get('/manage-settings')->assertRedirectToRoute('dashboard');
    });

    it('loads current settings on mount', function () {
        SchoolSetting::create(['key' => 'jumlah_ekskul',      'value' => '8',  'label' => 'Ekskul']);
        SchoolSetting::create(['key' => 'tingkat_akreditasi', 'value' => 'A+', 'label' => 'Akreditasi']);

        loginAsAdmin();

        Livewire\Livewire::test(ManageSettings::class)
            ->assertSet('jumlah_ekskul', '8')
            ->assertSet('tingkat_akreditasi', 'A+');
    });

    it('saves updated settings to database', function () {
        SchoolSetting::create(['key' => 'jumlah_ekskul',      'value' => '5',  'label' => 'Ekskul']);
        SchoolSetting::create(['key' => 'tingkat_akreditasi', 'value' => 'A',  'label' => 'Akreditasi']);

        loginAsAdmin();

        Livewire\Livewire::test(ManageSettings::class)
            ->set('jumlah_ekskul', '12')
            ->set('tingkat_akreditasi', 'A+')
            ->call('save');

        expect(SchoolSetting::get('jumlah_ekskul'))->toBe('12');
        expect(SchoolSetting::get('tingkat_akreditasi'))->toBe('A+');
    });

    it('validates jumlah_ekskul must be an integer', function () {
        SchoolSetting::create(['key' => 'jumlah_ekskul',      'value' => '5', 'label' => 'Ekskul']);
        SchoolSetting::create(['key' => 'tingkat_akreditasi', 'value' => 'A', 'label' => 'Akreditasi']);

        loginAsAdmin();

        Livewire\Livewire::test(ManageSettings::class)
            ->set('jumlah_ekskul', 'abc')
            ->call('save')
            ->assertHasErrors(['jumlah_ekskul']);
    });
});
