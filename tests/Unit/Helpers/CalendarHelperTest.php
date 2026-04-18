<?php

declare(strict_types=1);

use App\Livewire\AcademicCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('AcademicCalendar monthLabel computed', function () {
    it('returns Indonesian month name for January', function () {
        $component = Livewire\Livewire::test(AcademicCalendar::class)
            ->set('month', 1)
            ->set('year', 2025);

        expect($component->get('monthLabel'))->toContain('Januari');
    });

    it('returns Indonesian month name for December', function () {
        $component = Livewire\Livewire::test(AcademicCalendar::class)
            ->set('month', 12)
            ->set('year', 2025);

        expect($component->get('monthLabel'))->toContain('Desember');
    });

    it('includes year in month label', function () {
        $component = Livewire\Livewire::test(AcademicCalendar::class)
            ->set('month', 6)
            ->set('year', 2025);

        expect($component->get('monthLabel'))->toContain('2025');
    });
});
