<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model {
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'pertemuan_kelas_id',
        'murid_id',
        'status_kehadiran',
        'waktu_absen',
    ];

    public function pertemuanKelas() {
        return $this->belongsTo(PertemuanKelas::class);
    }

    public function murid() {
        return $this->belongsTo(ProfilMurid::class, 'murid_id');
    }
}
