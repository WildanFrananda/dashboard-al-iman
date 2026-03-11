<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMengajar extends Model {
    use HasFactory;

    protected $table = 'jadwal_mengajar';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
    ];

    public function guru() {
        return $this->belongsTo(ProfilGuru::class, 'guru_id');
    }

    public function mataPelajaran() {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function pertemuanKelas() {
        return $this->hasMany(PertemuanKelas::class);
    }
}
