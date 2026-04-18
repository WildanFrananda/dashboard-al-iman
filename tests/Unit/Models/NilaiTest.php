<?php

declare(strict_types=1);

use App\Models\Nilai;

it('casts nilai column as integer', function () {
    $nilai = Nilai::factory()->make(['nilai' => '85']);
    expect($nilai->nilai)->toBe(85);
});

it('casts semester column as integer', function () {
    $nilai = Nilai::factory()->make(['semester' => '1']);
    expect($nilai->semester)->toBe(1);
});

it('nilai within range 0-100 is valid', function (int $n) {
    $nilai = Nilai::factory()->make(['nilai' => $n]);
    expect($nilai->nilai)->toBeValidNilai();
})->with('valid_nilai_boundary');
