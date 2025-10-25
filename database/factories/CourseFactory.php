<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl('640', '480', 'education', true),
            'category_id' => \App\Models\Category::factory(),
            'user_id' => \App\Models\User::factory(),//instructor_id
        ];
    }
}
