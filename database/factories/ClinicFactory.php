<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
{
    /**
     * @var class-string<Clinic>
     */
    protected $model = Clinic::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'location' => Point::makeGeodetic(
                fake()->randomFloat(4, -9, -8),
                fake()->randomFloat(4, -47, -46),
            ),
            // 'location' => Point::make(fake()->latitude(), fake()->longitude()),
            'user_id' => User::factory(),
        ];
    }
}
