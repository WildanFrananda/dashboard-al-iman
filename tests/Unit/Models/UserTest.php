<?php

declare(strict_types=1);

use App\Models\User;

describe('User::initials()', function () {
    it('generates single initial from single-word name', function () {
        $user = User::factory()->make(['name' => 'Budi']);
        expect($user->initials())->toBe('B');
    });

    it('generates two initials from two-word name', function () {
        $user = User::factory()->make(['name' => 'Budi Santoso']);
        expect($user->initials())->toBe('BS');
    });

    it('generates two initials only from three-word name', function () {
        $user = User::factory()->make(['name' => 'Budi Eko Santoso']);
        expect($user->initials())->toBe('BE');
    });
});

describe('User role', function () {
    it('recognises admin role', function () {
        $user = User::factory()->make(['role' => 'admin']);
        expect($user->role)->toBe('admin');
    });

    it('recognises guru role', function () {
        $user = User::factory()->make(['role' => 'guru']);
        expect($user->role)->toBe('guru');
    });

    it('recognises murid role', function () {
        $user = User::factory()->make(['role' => 'murid']);
        expect($user->role)->toBe('murid');
    });
});

it('hides password from array serialization', function () {
    $user = User::factory()->make();
    expect(array_keys($user->toArray()))->not->toContain('password');
});
