<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\Kelas;
use Illuminate\Contracts\Console\Kernel;

Kelas::all()->each(function ($k) {
    if (preg_match('/Kelas (\d+)([A-Z]?)/', $k->nama_kelas, $m)) {
        $k->update([
            'level' => (int) $m[1],
            'kelompok' => $m[2] ?: null,
        ]);
        echo "Updated {$k->nama_kelas}: Level {$m[1]}, Kelompok ".($m[2] ?: 'null')."\n";
    }
});
