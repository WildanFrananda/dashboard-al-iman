<?php

declare(strict_types=1);

use App\Livewire\AdminGradeRecap;
use App\Livewire\AcademicCalendar;
use App\Livewire\AttendanceRecap;
use App\Livewire\Dashboard;
use App\Livewire\ManageUser;
use App\Models\AcademicEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    loginAsAdmin();
});

it('dashboard loads with fewer than 15 queries', function () {
    User::factory()->count(5)->create(['role' => 'murid']);
    AcademicEvent::factory()->count(3)->upcoming()->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    Livewire\Livewire::test(Dashboard::class);

    $queryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queryCount)->toBeLessThan(15);
});

it('calendar weeks computation uses a single DB query', function () {
    AcademicEvent::factory()->count(10)->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $component = Livewire\Livewire::test(AcademicCalendar::class);
    // Access computed property
    $component->call('nextMonth')->call('previousMonth');

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    // calendarWeeks() harus hanya 1 query untuk mengambil semua event
    $eventQueries = array_filter($queries, fn ($q) => str_contains($q['query'] ?? '', 'academic_events'));
    expect(count($eventQueries))->toBeLessThanOrEqual(3);
});

it('manage user list loads with fewer than 10 queries', function () {
    User::factory()->count(10)->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    Livewire\Livewire::test(ManageUser::class);

    $queryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queryCount)->toBeLessThan(10);
});

it('attendance recap does not produce N+1 queries', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    Livewire\Livewire::test(AttendanceRecap::class);

    $queryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queryCount)->toBeLessThan(15);
});

it('admin grade recap does not produce N+1 queries', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    Livewire\Livewire::test(AdminGradeRecap::class);

    $queryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queryCount)->toBeLessThan(15);
});
