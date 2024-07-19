<?php

namespace Database\Factories;

use App\Models\ControlPoint;
use App\Models\Drone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ControlPoint>
 */
class ControlPointFactory extends Factory
{
    protected $model = ControlPoint::class;

    public function definition(): array
    {
        $types = ['IMAGE', 'SIGNAL', 'NUMBER'];
        return [
            'data_type' => $this->faker->randomElement($types),
            'drone_id' => null, // Default value
            'latitude' => $this->faker->numberBetween(0,200),
            'longitude' => $this->faker->numberBetween(0,300),
        ];
    }

    public function withMissionId($missionId)
    {
        return $this->state(function (array $attributes) use ($missionId) {
            $droneId = null;
            if ($this->faker->boolean(25)) { // 25% pravdepodobnosť
                $droneId = $this->getRandomDroneId($missionId);
            }

            return [
                'drone_id' => $droneId,
                'mission_id' => $missionId,
            ];
        });
    }

    private function getRandomDroneId($missionId)
    {
        $drone = Drone::where('mission_id', $missionId)->inRandomOrder()->first();
        return $drone ? $drone->id : null;
    }
}

