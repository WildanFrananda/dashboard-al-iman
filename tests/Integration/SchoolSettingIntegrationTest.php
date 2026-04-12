<?php

declare(strict_types=1);

use App\Models\SchoolSetting;

it('get() retrieves seeded value from database', function () {
    SchoolSetting::create(['key' => 'jumlah_ekskul', 'value' => '12', 'label' => 'Ekskul']);

    expect(SchoolSetting::get('jumlah_ekskul', '0'))->toBe('12');
});

it('set() updates the value stored in database', function () {
    SchoolSetting::create(['key' => 'tingkat_akreditasi', 'value' => 'A', 'label' => 'Akreditasi']);

    SchoolSetting::set('tingkat_akreditasi', 'A+');

    expect(SchoolSetting::get('tingkat_akreditasi'))->toBe('A+');
});

it('set() does not create duplicate rows', function () {
    SchoolSetting::create(['key' => 'jumlah_ekskul', 'value' => '5', 'label' => 'Ekskul']);
    SchoolSetting::set('jumlah_ekskul', '8');

    expect(SchoolSetting::where('key', 'jumlah_ekskul')->count())->toBe(1);
});
