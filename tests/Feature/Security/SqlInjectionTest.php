<?php

declare(strict_types=1);

use App\Livewire\ManageUser;
use App\Livewire\ManageClass;
use App\Livewire\ManageStudent;

beforeEach(function () {
    loginAsAdmin();
});

it('search input in ManageUser is safe from SQL injection', function (string $payload) {
    Livewire\Livewire::test(ManageUser::class)
        ->set('search', $payload)
        ->assertOk(); // tidak throw DB error
})->with('sql_injection_payloads');

it('search input in ManageClass is safe from SQL injection', function (string $payload) {
    Livewire\Livewire::test(ManageClass::class)
        ->set('search', $payload)
        ->assertOk();
})->with('sql_injection_payloads');

it('search input in ManageStudent is safe from SQL injection', function (string $payload) {
    Livewire\Livewire::test(ManageStudent::class)
        ->set('search', $payload)
        ->assertOk();
})->with('sql_injection_payloads');
