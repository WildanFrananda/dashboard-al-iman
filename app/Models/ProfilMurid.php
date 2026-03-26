<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilMurid extends Model {
    use HasFactory;

    protected $table = 'profil_murid';

    protected $fillable = [
        'user_id',
        'nis',
        'nama_lengkap',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function absensis() {
        return $this->hasMany(Absensi::class, 'murid_id');
    }

    public function kelas() {
        return $this->belongsToMany(Kelas::class, 'kelas_murid', 'murid_id', 'kelas_id')
            ->withPivot('tahun_ajaran')
            ->withTimestamps();
    }
}
