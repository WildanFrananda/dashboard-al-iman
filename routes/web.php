<?php

declare(strict_types = 1)
;

use App\Livewire\Attendance;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Livewire\TeacherAttendance;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// create a route that raised an exception for testing purpose
Route::get('/error', function () {
    throw new Exception('This is a test exception for Sentry integration.');
})->name('error.test');

Route::get('/login', Login::class)->name('login');

Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/attendance', Attendance::class)->name('attendance');
Route::get('/teacher-attendance', TeacherAttendance::class)->name('teacher-attendance');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
        when(
        Features::canManageTwoFactorAuthentication()
        && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
    ['password.confirm'],
    [],
    ),
    )
        ->name('two-factor.show');
});

Route::get('/health', fn() => response()->json(['status' => 'ok']));
