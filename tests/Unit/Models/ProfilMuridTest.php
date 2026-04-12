<?php

declare(strict_types=1);

use App\Models\ProfilMurid;

it('has aktif status by default', function () {
    $murid = ProfilMurid::factory()->make();
    expect($murid->status)->toBe('aktif');
});

it('can be created with lulus status', function () {
    $murid = ProfilMurid::factory()->make(['status' => 'lulus']);
    expect($murid->status)->toBe('lulus');
});

it('can be created with pindah status', function () {
    $murid = ProfilMurid::factory()->make(['status' => 'pindah']);
    expect($murid->status)->toBe('pindah');
});
