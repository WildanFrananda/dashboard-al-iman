<?php

declare(strict_types=1);

use App\Models\SchoolSetting;

it('get() retrieves seeded value from database', function () {
    // Migration seeds jumlah_ekskul = '0'; update it to '12' via set()
    SchoolSetting::set('jumlah_ekskul', '12');

    expect(SchoolSetting::get('jumlah_ekskul', '0'))->toBe('12');
});

it('set() updates the value stored in database', function () {
    // Migration seeds tingkat_akreditasi = '-'; update it via set()
    SchoolSetting::set('tingkat_akreditasi', 'A+');

    expect(SchoolSetting::get('tingkat_akreditasi'))->toBe('A+');
});

it('set() does not create duplicate rows', function () {
    SchoolSetting::set('jumlah_ekskul', '5');
    SchoolSetting::set('jumlah_ekskul', '8');

    expect(SchoolSetting::where('key', 'jumlah_ekskul')->count())->toBe(1);
});
