<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // 1. Reset data yang rusak dari percobaan sebelumnya
    DB::table('kelas_murid')->where('tahun_ajaran', '2027/2028')->delete();
    DB::table('kelas')->where('tahun_ajaran', '2027/2028')->delete();
    DB::table('profil_murid')->where('status', 'lulus')->update(['status' => 'aktif']);

    // 2. Fix Level & Kelompok menggunakan Raw Query agar pasti masuk ke DB
    $classes = DB::table('kelas')->get();
    foreach ($classes as $k) {
        if (preg_match('/Kelas (\d+)([A-Z]?)/', $k->nama_kelas, $m)) {
            DB::table('kelas')->where('id', $k->id)->update([
                'level' => (int) $m[1],
                'kelompok' => $m[2] ?: null,
            ]);
            echo "Fixed ID {$k->id}: {$k->nama_kelas} -> Level {$m[1]}, Kelompok ".($m[2] ?: 'null')."\n";
        }
    }

    DB::commit();
    echo "RESET AND FIX SUCCESSFUL.\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "ERROR: ".$e->getMessage()."\n";
}
