<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertemuanKelas extends Model
{
    use HasFactory;

    protected $table = 'pertemuan_kelas';

    protected $fillable = [
        'jadwal_mengajar_id',
        'tanggal_pertemuan',
        'materi',
    ];

    public function jadwalMengajar()
    {
        return $this->belongsTo(JadwalMengajar::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
