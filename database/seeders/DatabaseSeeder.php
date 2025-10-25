<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create some instructors and students
        $instructors=User::factory(5)->create(['role' => 'instructor']);
        $students=User::factory(20)->create(['role' => 'student']);
        // Create some categories
        $categories=Category::factory(5)->create();
        // Create courses for each instructor
        foreach($instructors as $instructor){
            $courses=\App\Models\Course::factory(3)->create([
                'user_id' => $instructor->id,
                'category_id' => $categories->random()->id,
            ]);
            // Create lessons for each course
            foreach($courses as $course){
                \App\Models\Lesson::factory(5)->create([
                    'course_id' => $course->id,
                ]);
            }}
            // Enroll students in random courses
            foreach($students as $student){
                $enrolledCourses=$courses->random(2);
                foreach($enrolledCourses as $course){
                    \App\Models\Enrollment::factory()->create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ]);
                    // Add comments and ratings
                    \App\Models\Comment::factory()->create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ]);
                    \App\Models\Rating::factory()->create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ]); 
                }}


    }
}
