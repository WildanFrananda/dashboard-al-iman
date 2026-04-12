<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicEvent>
 */
class AcademicEventFactory extends Factory {
    protected $model = AcademicEvent::class;

    public function definition(): array {
        $category = fake()->randomElement(['libur', 'ujian', 'kegiatan', 'umum']);

        return [
            'title' => fake()->sentence(4),
            'category' => $category,
            'start_date' => now()->addDays(fake()->numberBetween(-15, 15))->format('Y-m-d'),
            'end_date' => null,
            'location' => fake()->city(),
            'description' => fake()->sentence(),
            'color' => AcademicEvent::defaultColor($category),
        ];
    }

    public function upcoming(): static {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->addDays(fake()->numberBetween(1, 30))->format('Y-m-d'),
            'end_date' => null,
        ]);
    }

    public function past(): static {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDays(fake()->numberBetween(1, 30))->format('Y-m-d'),
            'end_date' => now()->subDay()->format('Y-m-d'),
        ]);
    }

    public function ongoing(): static {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDay()->format('Y-m-d'),
        ]);
    }

    public function libur(): static {
        return $this->state(fn (array $attributes) => [
            'category' => 'libur',
            'color' => '#EF4444',
        ]);
    }
}
