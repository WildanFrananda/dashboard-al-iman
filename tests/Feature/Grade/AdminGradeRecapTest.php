<?php

declare(strict_types=1);

use App\Livewire\AdminGradeRecap;

it('allows admin to access admin-grade-recap', function () {
    loginAsAdmin();
    Livewire\Livewire::test(AdminGradeRecap::class)->assertOk();
});

it('blocks murid from accessing admin-grade-recap', function () {
    loginAsMurid();
    $this->get('/admin-grade-recap')->assertRedirectToRoute('dashboard');
});
