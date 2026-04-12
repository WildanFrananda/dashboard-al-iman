<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\ProfilGuru;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeachingSchedule>
 */
class TeachingScheduleFactory extends Factory {
    protected $model = TeachingSchedule::class;

    public function definition(): array {
        return [
            'guru_id' => ProfilGuru::factory(),
            'subject_id' => Subject::factory(),
            'kelas_id' => Kelas::factory(),
            'hari' => fake()->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
        ];
    }
}
