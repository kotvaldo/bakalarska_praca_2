<?php

namespace Database\Factories;

use App\Models\Drone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Drone>
 */
class DroneFactory extends Factory
{
    protected $model = Drone::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        $types = ['IMAGE', 'SIGNAL', 'NUMBER'];

        return [
            'name' => 'DRONE-' . strtoupper($this->faker->unique()->lexify('???')) . $this->faker->unique()->numerify('###'),
            'type' => $this->faker->randomElement($types),
            'serial_number' => $this->faker->unique()->numerify('SN############'),
        ];
    }

}
