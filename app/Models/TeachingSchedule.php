<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingSchedule extends Model {
    use HasFactory;

    protected $table = 'teaching_schedules';

    protected $fillable = [
        'guru_id',
        'subject_id',
        'kelas_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    public function guru() {
        return $this->belongsTo(ProfilGuru::class, 'guru_id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }

    public function pertemuanKelas() {
        return $this->hasMany(PertemuanKelas::class);
    }
}
