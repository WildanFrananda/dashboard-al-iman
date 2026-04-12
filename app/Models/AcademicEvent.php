<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicEvent extends Model {
    use HasFactory;

    protected $table = 'academic_events';

    protected $fillable = [
        'title',
        'category',
        'start_date',
        'end_date',
        'location',
        'description',
        'color',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /** Warna default per kategori. */
    public static function defaultColor(string $category): string {
        return match($category) {
            'libur'    => '#EF4444',
            'ujian'    => '#F28B2B',
            'kegiatan' => '#10B981',
            default    => '#0F609B',
        };
    }

    /** Scope: event yang akan datang atau sedang berlangsung. */
    public function scopeUpcoming(Builder $query): Builder {
        return $query->where(function ($q) {
            $today = today();
            $q->where('start_date', '>=', $today)
              ->orWhere(function ($q2) use ($today) {
                  $q2->whereNotNull('end_date')->where('end_date', '>=', $today);
              });
        })->orderBy('start_date');
    }
}
