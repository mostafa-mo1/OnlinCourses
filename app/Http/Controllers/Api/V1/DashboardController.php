<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Rating;

class DashboardController extends Controller
{
    
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'instructor') {
            return response()->json([
                'message' => 'Only instructors can access this dashboard data.'
            ], 401);
        }
        $totalCourses = Course::count();
        //your courses
        $totalYourCourses = Course::where('user_id', $user->id)->count();
        //total students enrolled in your courses
        $totalStudents = Enrollment::whereHas('course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->distinct('user_id')->count('user_id');
        //total comments on your courses
        $commentCount = Comment::whereHas('course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        //total ratings on your courses
        $ratingCount = Rating::whereHas('course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        $avrRating = Rating::whereHas('course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->avg('rating');
        return response()->json([
            'data'=>[
            'total_courses' => $totalCourses,
            'total_your_courses' => $totalYourCourses,
            'total_students_enrolled' => $totalStudents,
            'total_comments' => $commentCount,
            'total_ratings' => $ratingCount,
            'average_rating' => round($avrRating, 2),
    ]]);
        

            }   
}
