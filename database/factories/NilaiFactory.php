<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nilai>
 */
class NilaiFactory extends Factory
{
    protected $model = Nilai::class;

    public function definition(): array
    {
        return [
            'murid_id'    => ProfilMurid::factory(),
            'subject_id'  => Subject::factory(),
            'guru_id'     => ProfilGuru::factory(),
            'kelas_id'    => Kelas::factory(),
            'tipe_nilai'  => fake()->randomElement(['UTS', 'UAS']),
            'nilai'       => fake()->numberBetween(0, 100),
            'semester'    => fake()->randomElement([1, 2]),
            'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1),
            'keterangan'  => null,
        ];
    }

    public function uts(): static
    {
        return $this->state(fn (array $attributes) => ['tipe_nilai' => 'UTS']);
    }

    public function uas(): static
    {
        return $this->state(fn (array $attributes) => ['tipe_nilai' => 'UAS']);
    }
}
