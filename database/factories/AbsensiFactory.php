<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\PertemuanKelas;
use App\Models\ProfilMurid;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absensi>
 */
class AbsensiFactory extends Factory
{
    protected $model = Absensi::class;

    public function definition(): array
    {
        return [
            'pertemuan_kelas_id' => PertemuanKelas::factory(),
            'murid_id'           => ProfilMurid::factory(),
            'status_kehadiran'   => fake()->randomElement(['Hadir', 'Izin', 'Sakit', 'Alpa']),
            'waktu_absen'        => now(),
        ];
    }

    public function hadir(): static
    {
        return $this->state(fn (array $attributes) => ['status_kehadiran' => 'Hadir']);
    }

    public function alpa(): static
    {
        return $this->state(fn (array $attributes) => ['status_kehadiran' => 'Alpa']);
    }
}
