<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model {
    use HasFactory;

    protected $table = 'nilais';

    protected $fillable = [
        'murid_id',
        'subject_id',
        'guru_id',
        'kelas_id',
        'tipe_nilai',
        'nilai',
        'kkm',
        'semester',
        'tahun_ajaran',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'integer',
        'kkm' => 'integer',
        'semester' => 'integer',
    ];

    public function murid() {
        return $this->belongsTo(ProfilMurid::class, 'murid_id');
    }

    public function guru() {
        return $this->belongsTo(ProfilGuru::class, 'guru_id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }
}
