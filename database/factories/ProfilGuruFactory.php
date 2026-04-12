<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ProfilGuru;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfilGuru>
 */
class ProfilGuruFactory extends Factory {
    protected $model = ProfilGuru::class;

    public function definition(): array {
        return [
            'user_id' => User::factory()->guru(),
            'nip' => fake()->unique()->numerify('####################'),
            'nama_lengkap' => fake()->name(),
        ];
    }
}
