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
    ];

    public function guru() {
        return $this->belongsTo(ProfilGuru::class, 'guru_id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function pertemuanKelas() {
        return $this->hasMany(PertemuanKelas::class);
    }
}
