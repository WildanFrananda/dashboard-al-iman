<?php

declare(strict_types=1);

use App\Models\AcademicEvent;

describe('AcademicEvent::defaultColor()', function () {
    it('returns red for libur category', function () {
        expect(AcademicEvent::defaultColor('libur'))->toBe('#EF4444');
    });

    it('returns orange for ujian category', function () {
        expect(AcademicEvent::defaultColor('ujian'))->toBe('#F28B2B');
    });

    it('returns green for kegiatan category', function () {
        expect(AcademicEvent::defaultColor('kegiatan'))->toBe('#10B981');
    });

    it('returns blue for umum category', function () {
        expect(AcademicEvent::defaultColor('umum'))->toBe('#0F609B');
    });

    it('returns default blue for unknown category', function () {
        expect(AcademicEvent::defaultColor('invalid'))->toBe('#0F609B');
    });

    it('every returned color is a valid hex value', function (string $category) {
        $color = AcademicEvent::defaultColor($category);
        expect($color)->toMatch('/^#[0-9A-Fa-f]{6}$/');
    })->with(['libur', 'ujian', 'kegiatan', 'umum', 'unknown']);
});

describe('AcademicEvent date casts', function () {
    it('casts start_date as Carbon instance', function () {
        $event = AcademicEvent::factory()->make(['start_date' => '2025-06-01']);
        expect($event->start_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('casts end_date as Carbon instance when present', function () {
        $event = AcademicEvent::factory()->make([
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-05',
        ]);
        expect($event->end_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('allows null end_date', function () {
        $event = AcademicEvent::factory()->make(['end_date' => null]);
        expect($event->end_date)->toBeNull();
    });
});
