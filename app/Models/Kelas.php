<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model {
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'kode_kelas',
        'nama_kelas',
        'tahun_ajaran',
        'wali_kelas_id',
        'level',
        'kelompok',
    ];

    public function waliKelas() {
        return $this->belongsTo(ProfilGuru::class, 'wali_kelas_id');
    }

    public function murids() {
        return $this->belongsToMany(ProfilMurid::class, 'kelas_murid', 'kelas_id', 'murid_id')
            ->withPivot('tahun_ajaran')
            ->withTimestamps();
    }

    public function teachingSchedules() {
        return $this->hasMany(TeachingSchedule::class);
    }
}
