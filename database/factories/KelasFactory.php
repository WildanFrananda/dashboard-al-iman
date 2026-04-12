<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory {
    protected $model = Kelas::class;

    public function definition(): array {
        $level = fake()->numberBetween(1, 6);
        $kelompok = fake()->randomElement(['A', 'B', 'C']);
        $tahun = date('Y').'/'.(date('Y') + 1);

        return [
            'kode_kelas' => 'SD-'.$level.$kelompok.'-'.fake()->unique()->numerify('###'),
            'nama_kelas' => "Kelas {$level}{$kelompok}",
            'tahun_ajaran' => $tahun,
            'level' => $level,
            'kelompok' => $kelompok,
            'wali_kelas_id' => null,
        ];
    }

    public function level(int $level): static {
        return $this->state(fn (array $attributes) => [
            'level' => $level,
            'nama_kelas' => "Kelas {$level}".($attributes['kelompok'] ?? 'A'),
        ]);
    }
}
