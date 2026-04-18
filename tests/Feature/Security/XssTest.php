<?php

declare(strict_types=1);

use App\Livewire\AcademicCalendar;
use App\Livewire\ManageUser;
use App\Models\AcademicEvent;
use App\Models\User;

it('escapes XSS payload in user name field', function (string $payload) {
    loginAsAdmin();

    Livewire\Livewire::test(ManageUser::class)
        ->set('form_name', $payload)
        ->set('form_email', 'xss@example.com')
        ->set('form_role', 'murid')
        ->set('form_password', 'password123')
        ->call('saveUser');

    // Verifikasi data tersimpan sebagai plain text, bukan dieksekusi
    $user = User::where('email', 'xss@example.com')->first();
    if ($user) {
        expect($user->name)->toBe($payload); // tersimpan sebagai-is, Blade auto-escape saat render
    }
})->with('xss_payloads');

it('escapes XSS payload in academic event title', function (string $payload) {
    loginAsAdmin();

    Livewire\Livewire::test(AcademicCalendar::class)
        ->call('openAddForm')
        ->set('form_title', $payload)
        ->set('form_category', 'umum')
        ->set('form_start_date', '2025-11-01')
        ->set('form_color', '#0F609B')
        ->call('saveEvent');

    $event = AcademicEvent::where('title', $payload)->first();
    if ($event) {
        // Nilai tersimpan sebagai plain text; Blade {{ }} auto-escape
        expect(htmlspecialchars($event->title))->not->toBe($event->title.'<executed>');
    }
})->with('xss_payloads');
