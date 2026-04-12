<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilGuru extends Model {
    use HasFactory;

    protected $table = 'profil_guru';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function teachingSchedules() {
        return $this->hasMany(TeachingSchedule::class, 'guru_id');
    }

    public function kelasWali() {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function nilais() {
        return $this->hasMany(Nilai::class, 'guru_id');
    }
}
