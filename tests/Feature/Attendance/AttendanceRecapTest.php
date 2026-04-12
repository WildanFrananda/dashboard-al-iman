<?php

declare(strict_types=1);

use App\Livewire\AttendanceRecap;

it('allows admin to access attendance-recap component', function () {
    loginAsAdmin();
    Livewire\Livewire::test(AttendanceRecap::class)->assertOk();
});

it('allows guru to access attendance-recap component', function () {
    loginAsGuru();
    Livewire\Livewire::test(AttendanceRecap::class)->assertOk();
});

it('aborts 403 when murid tries to access attendance-recap component', function () {
    loginAsMurid();
    Livewire\Livewire::test(AttendanceRecap::class)->assertForbidden();
});
