<?php

namespace Database\Factories;

use App\Models\Drone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ControlPoint>
 */
class ControlPointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['IMAGE', 'SIGNAL', 'NUMBER'];
        return [
            'data_type' => $this->faker->randomElement($types),
            'drone_id' => $this->getRandomDroneId(),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }

    private function getRandomDroneId()
    {
        if ($this->faker->boolean(25)) {
            $drone = Drone::inRandomOrder()->first();
            return $drone ? $drone->id : null;
        }
        return null;
    }
}
