<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_time = $this->faker->numberBetween(8, 18); // Hora entre 8 y 18
        $end_time = $this->faker->numberBetween($start_time + 1, 20); //me aseguro que sea mayor a start_time
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'workspace_id' => Workspace::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'date' => Carbon::now()->addDays($this->faker->numberBetween(0, 10))->format('Y-m-d'),
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];
    }
}
