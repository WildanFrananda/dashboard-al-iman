<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PertemuanKelas;
use App\Models\TeachingSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PertemuanKelas>
 */
class PertemuanKelasFactory extends Factory
{
    protected $model = PertemuanKelas::class;

    public function definition(): array
    {
        return [
            'teaching_schedule_id' => TeachingSchedule::factory(),
            'tanggal_pertemuan'    => now()->addDays(fake()->numberBetween(-15, 15))->format('Y-m-d'),
            'materi'               => fake()->sentence(),
        ];
    }
}
