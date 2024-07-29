<?php

namespace Database\Factories;

use App\Models\ControlPoint;
use App\Models\Drone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ControlPoint>
 */
class ControlPointFactory extends Factory
{
    protected $model = ControlPoint::class;

    public function definition(): array
    {
        $types = ['IMAGE', 'SIGNAL', 'NUMBER'];
        Log::info('Generating default values for ControlPoint', ['types' => $types]);

        $data = [
            'data_type' => $this->faker->randomElement($types),
            'drone_id' => null, // Default value
            'latitude' => $this->faker->latitude(), // Použitie faker metódy na generovanie realistických hodnôt
            'longitude' => $this->faker->longitude(), // Použitie faker metódy na generovanie realistických hodnôt
        ];

        Log::info('Generated ControlPoint data', $data);

        return $data;
    }

    public function withMissionId($missionId)
    {
        Log::info('Setting state with mission ID', ['mission_id' => $missionId]);

        return $this->state(function (array $attributes) use ($missionId) {
            $types = ['IMAGE', 'SIGNAL', 'NUMBER'];
            $droneId = null;
            $data_type = null;

            Log::info('Determining drone ID and data type', ['mission_id' => $missionId]);

            if ($this->faker->boolean(25)) { // 25% pravdepodobnosť
                $droneId = $this->getRandomDroneId($missionId);
                Log::info('Random drone ID selected', ['drone_id' => $droneId]);

                if ($droneId) {
                    $drone = Drone::find($droneId);
                    if ($drone) {
                        Log::info('Drone found', ['drone' => $drone]);
                        $data_type = $drone->type; // Oprava: používanie správneho atribútu `type`
                        Log::info('Drone data_type value', ['data_type' => $data_type]);
                    } else {
                        $data_type = $this->faker->randomElement($types);
                        Log::warning('Drone not found, random data type selected', ['data_type' => $data_type]);
                    }
                }
            } else {
                $data_type = $this->faker->randomElement($types);
                Log::info('Random data type selected', ['data_type' => $data_type]);
            }

            $stateData = [
                'drone_id' => $droneId,
                'mission_id' => $missionId,
                'data_type' => $data_type,
            ];

            Log::info('State data for ControlPoint', $stateData);

            return $stateData;
        });
    }

    private function getRandomDroneId($missionId)
    {
        Log::info('Fetching random drone ID for mission', ['mission_id' => $missionId]);

        $drone = Drone::where('mission_id', $missionId)->inRandomOrder()->first();

        if ($drone) {
            Log::info('Random drone ID fetched', ['drone_id' => $drone->id]);
        } else {
            Log::warning('No drone found for mission', ['mission_id' => $missionId]);
        }

        return $drone ? $drone->id : null;
    }
}
