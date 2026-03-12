<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model {
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'subject_code',
        'subject_name',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teachingSchedules() {
        return $this->hasMany(TeachingSchedule::class);
    }
}
