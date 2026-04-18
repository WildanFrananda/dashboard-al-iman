<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertemuanKelas extends Model {
    use HasFactory;

    protected $table = 'pertemuan_kelas';

    protected $fillable = [
        'teaching_schedule_id',
        'tanggal_pertemuan',
        'materi',
    ];

    public function teachingSchedule() {
        return $this->belongsTo(TeachingSchedule::class);
    }

    public function absensis() {
        return $this->hasMany(Absensi::class);
    }
}
