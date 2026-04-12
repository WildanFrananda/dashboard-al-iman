<?php

declare(strict_types=1);

use App\Models\SchoolSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns default value when key does not exist in database', function () {
    expect(SchoolSetting::get('nonexistent_key', 'fallback'))->toBe('fallback');
});

it('returns empty string as default when no default is provided', function () {
    expect(SchoolSetting::get('nonexistent_key'))->toBe('');
});

it('returns stored value from database', function () {
    SchoolSetting::create(['key' => 'test_key', 'value' => 'test_value', 'label' => 'Test']);
    expect(SchoolSetting::get('test_key'))->toBe('test_value');
});

it('updates value in database via set()', function () {
    SchoolSetting::create(['key' => 'ekskul', 'value' => '5', 'label' => 'Ekskul']);
    SchoolSetting::set('ekskul', '10');
    expect(SchoolSetting::get('ekskul'))->toBe('10');
});
