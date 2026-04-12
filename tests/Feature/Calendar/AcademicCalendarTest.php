<?php

declare(strict_types=1);

use App\Livewire\AcademicCalendar;
use App\Models\AcademicEvent;

describe('AcademicCalendar — navigation', function () {
    beforeEach(function () {
        loginAsAdmin();
    });

    it('renders calendar for current month on mount', function () {
        Livewire\Livewire::test(AcademicCalendar::class)
            ->assertSet('month', now()->month)
            ->assertSet('year', now()->year);
    });

    it('navigates to previous month on previousMonth call', function () {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2025, 6, 15));

        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('previousMonth')
            ->assertSet('month', 5)
            ->assertSet('year', 2025);

        \Carbon\Carbon::setTestNow();
    });

    it('navigates to next month on nextMonth call', function () {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2025, 6, 15));

        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('nextMonth')
            ->assertSet('month', 7)
            ->assertSet('year', 2025);

        \Carbon\Carbon::setTestNow();
    });

    it('wraps month to December when going back from January', function () {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2025, 1, 15));

        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('previousMonth')
            ->assertSet('month', 12)
            ->assertSet('year', 2024);

        \Carbon\Carbon::setTestNow();
    });

    it('wraps month to January when going forward from December', function () {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2025, 12, 15));

        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('nextMonth')
            ->assertSet('month', 1)
            ->assertSet('year', 2026);

        \Carbon\Carbon::setTestNow();
    });
});

describe('AcademicCalendar — admin CRUD', function () {
    beforeEach(function () {
        loginAsAdmin();
    });

    it('opens add form modal for admin', function () {
        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('openAddForm')
            ->assertSet('showFormModal', true)
            ->assertSet('editingId', null);
    });

    it('saves new academic event to database', function () {
        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('openAddForm')
            ->set('form_title', 'Hari Guru Nasional')
            ->set('form_category', 'kegiatan')
            ->set('form_start_date', '2025-11-25')
            ->set('form_color', '#10B981')
            ->call('saveEvent');

        expect(AcademicEvent::where('title', 'Hari Guru Nasional')->exists())->toBeTrue();
    });

    it('validates event title is required', function () {
        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('openAddForm')
            ->set('form_title', '')
            ->set('form_start_date', '2025-11-25')
            ->call('saveEvent')
            ->assertHasErrors(['form_title']);
    });

    it('validates end_date must be after or equal to start_date', function () {
        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('openAddForm')
            ->set('form_title', 'Test Event')
            ->set('form_start_date', '2025-11-25')
            ->set('form_end_date', '2025-11-20')
            ->call('saveEvent')
            ->assertHasErrors(['form_end_date']);
    });

    it('deletes academic event from database', function () {
        $event = AcademicEvent::factory()->create();

        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('deleteEvent', $event->id);

        expect(AcademicEvent::find($event->id))->toBeNull();
    });
});

describe('AcademicCalendar — authorization', function () {
    it('aborts 403 when non-admin tries to open add form', function () {
        loginAsMurid();
        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('openAddForm')
            ->assertForbidden();
    });

    it('aborts 403 when guru tries to delete an event', function () {
        loginAsGuru();
        $event = AcademicEvent::factory()->create();
        Livewire\Livewire::test(AcademicCalendar::class)
            ->call('deleteEvent', $event->id)
            ->assertForbidden();
    });
});
