<?php

declare(strict_types=1);

use App\Models\AcademicEvent;

describe('AcademicEvent DB operations', function () {
    it('saves event to database', function () {
        AcademicEvent::factory()->create(['title' => 'Ujian Tengah Semester']);

        expect(AcademicEvent::count())->toBe(1);
        expect(AcademicEvent::first()->title)->toBe('Ujian Tengah Semester');
    });

    it('scopeUpcoming returns future and ongoing events only', function () {
        AcademicEvent::factory()->past()->create();    // tidak masuk
        AcademicEvent::factory()->upcoming()->create(); // masuk
        AcademicEvent::factory()->ongoing()->create();  // masuk

        $results = AcademicEvent::upcoming()->get();
        expect($results)->toHaveCount(2);
    });

    it('scopeUpcoming includes ongoing multi-day events', function () {
        AcademicEvent::factory()->create([
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDay()->format('Y-m-d'),
        ]);

        expect(AcademicEvent::upcoming()->get())->toHaveCount(1);
    });

    it('excludes fully past events from scopeUpcoming', function () {
        AcademicEvent::factory()->past()->create();

        expect(AcademicEvent::upcoming()->get())->toHaveCount(0);
    });
});
