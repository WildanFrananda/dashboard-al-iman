<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ProfilMurid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfilMurid>
 */
class ProfilMuridFactory extends Factory {
    protected $model = ProfilMurid::class;

    public function definition(): array {
        return [
            'user_id' => User::factory()->murid(),
            'nis' => fake()->unique()->numerify('##########'),
            'nama_lengkap' => fake()->name(),
            'status' => 'aktif',
        ];
    }

    public function lulus(): static {
        return $this->state(fn (array $attributes) => ['status' => 'lulus']);
    }

    public function pindah(): static {
        return $this->state(fn (array $attributes) => ['status' => 'pindah']);
    }
}
