<?php

namespace Database\Factories;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'average_rating' =>$this->faker->numberBetween(1, 5),
            'user_id' => \App\Models\User::factory(),
            'course_id' => \App\Models\Course::factory(),
        ];
    }
}
