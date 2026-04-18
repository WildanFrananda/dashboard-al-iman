<?php

declare(strict_types=1);

use App\Models\Subject;

it('casts is_active as boolean true when given 1', function () {
    $subject = Subject::factory()->make(['is_active' => 1]);
    expect($subject->is_active)->toBeTrue();
});

it('casts is_active as boolean false when given 0', function () {
    $subject = Subject::factory()->make(['is_active' => 0]);
    expect($subject->is_active)->toBeFalse();
});

it('is active by default', function () {
    $subject = Subject::factory()->make();
    expect($subject->is_active)->toBeTrue();
});
