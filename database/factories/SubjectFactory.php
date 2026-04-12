<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory {
    protected $model = Subject::class;

    public function definition(): array {
        return [
            'subject_code' => strtoupper(fake()->unique()->lexify('???-###')),
            'subject_name' => fake()->words(3, true),
            'category' => fake()->randomElement(['Wajib', 'Muatan Lokal', 'Ekstrakurikuler']),
            'is_active' => true,
        ];
    }

    public function inactive(): static {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
