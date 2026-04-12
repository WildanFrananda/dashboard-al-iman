<?php

declare(strict_types=1);

use App\Livewire\ManageGrade;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ManageGrade tahunAjaran on mount', function () {
    it('formats tahun ajaran as YYYY/YYYY pattern', function () {
        loginAsGuru();
        Livewire\Livewire::test(ManageGrade::class)
            ->assertSet('tahunAjaran', fn ($v) => preg_match('/^\d{4}\/\d{4}$/', $v) === 1);
    });

    it('sets semester to 1 when current month is July or later', function () {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2025, 8, 1));
        loginAsGuru();

        Livewire\Livewire::test(ManageGrade::class)
            ->assertSet('semester', 1);

        \Carbon\Carbon::setTestNow();
    });

    it('sets semester to 2 when current month is before July', function () {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2025, 3, 1));
        loginAsGuru();

        Livewire\Livewire::test(ManageGrade::class)
            ->assertSet('semester', 2);

        \Carbon\Carbon::setTestNow();
    });
});
