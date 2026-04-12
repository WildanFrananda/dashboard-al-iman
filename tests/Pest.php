<?php

declare(strict_types=1);

use App\Models\Kelas;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Integration');

pest()->extend(TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Custom Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeAdmin', function () {
    return $this->toBe('admin', $this->value->role ?? $this->value);
});

expect()->extend('toBeGuru', function () {
    return $this->toBe('guru', $this->value->role ?? $this->value);
});

expect()->extend('toBeMurid', function () {
    return $this->toBe('murid', $this->value->role ?? $this->value);
});

expect()->extend('toBeAktif', function () {
    return $this->toBe('aktif', $this->value->status ?? $this->value);
});

expect()->extend('toBeLulus', function () {
    return $this->toBe('lulus', $this->value->status ?? $this->value);
});

expect()->extend('toHaveValidHexColor', function () {
    $value = is_object($this->value) ? $this->value->color ?? $this->value : $this->value;
    expect(preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $value))->toBe(1);

    return $this;
});

expect()->extend('toBeValidNilai', function () {
    $value = $this->value;
    expect($value)->toBeGreaterThanOrEqual(0)->toBeLessThanOrEqual(100);

    return $this;
});

/*
|--------------------------------------------------------------------------
| Global Datasets
|--------------------------------------------------------------------------
*/

dataset('xss_payloads', [
    '<script>alert(1)</script>',
    '<img src=x onerror=alert(1)>',
    'javascript:alert(1)',
]);

dataset('sql_injection_payloads', [
    ["' OR '1'='1"],
    ["'; DROP TABLE users; --"],
    ['1 UNION SELECT * FROM users'],
]);

dataset('invalid_nilai', [[-1], [101], [200], [-999]]);

dataset('valid_nilai_boundary', [[0], [1], [99], [100]]);

dataset('non_admin_roles', [['murid'], ['guru']]);

/*
|--------------------------------------------------------------------------
| Global Helper Functions
|--------------------------------------------------------------------------
*/

/**
 * Buat user admin dan langsung actingAs.
 */
function loginAsAdmin(): User {
    $user = User::factory()->admin()->create();
    test()->actingAs($user);

    return $user;
}

/**
 * Buat user guru dengan ProfilGuru dan langsung actingAs.
 */
function loginAsGuru(): array {
    $user = User::factory()->guru()->create();
    $profil = ProfilGuru::factory()->create(['user_id' => $user->id]);
    test()->actingAs($user);

    return ['user' => $user, 'profil' => $profil];
}

/**
 * Buat user murid dengan ProfilMurid dan langsung actingAs.
 */
function loginAsMurid(): array {
    $user = User::factory()->murid()->create();
    $profil = ProfilMurid::factory()->create(['user_id' => $user->id]);
    test()->actingAs($user);

    return ['user' => $user, 'profil' => $profil];
}

/**
 * Buat kelas lengkap dengan sejumlah murid terpasang.
 */
function createKelasWithStudents(int $count = 3, string $tahunAjaran = '2025/2026'): array {
    $kelas = Kelas::factory()->create(['tahun_ajaran' => $tahunAjaran]);
    $murids = ProfilMurid::factory()->count($count)->create();

    foreach ($murids as $murid) {
        $kelas->murids()->attach($murid->id, ['tahun_ajaran' => $tahunAjaran]);
    }

    return ['kelas' => $kelas, 'murids' => $murids];
}
