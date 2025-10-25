<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Notifications\StudentEnrolledNotification;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role !== 'student') {
            return response()->json([
                'status' => false,
                'message' => 'Only students can view their enrollments.'
            ], 403);
        }
        $enrollments = $user->enrolledCourses()->with('category','instructor')->get();
        return response()->json([
            'status' => true,
            'data' => $enrollments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($courseId)
    {
        //enroll in a course
        $user = auth()->user();
        $course=Course::find($courseId);
        if(!$course){
            return response()->json([  'status'=>false,
            'message'=>'Course not found'
        ],404); 
        }
        if($course->user_id ===$user->id){
            return response()->json([  
                'status'=>false,
                'message'=>'You cannot enroll in your own course'
            ],403); 
        }
        $exist=Enrollment::where('user_id',$user->id)->where('course_id',$courseId)->first();
        if($exist){
            return response()->json([  
                'status'=>false,
                'message'=>'You are already enrolled in this course'
            ],409);
        }
        $enrollment=Enrollment::create([
            'user_id'=>$user->id,
            'course_id'=>$courseId,
        ]);;
            // ......Notify Instructor\\\\\\\
        $instructor=$course->instructor;
        $instructor->notify(new StudentEnrolledNotification($course));

        return response()->json([  
            'status'=>true,
            'message'=>'Enrolled successfully',
            'data'=>$enrollment
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseId)
    {
        //cancel enrollment
        $user=auth()->user();
        $enrollment=Enrollment::where('user_id',$user->id)
        ->where('course_id',$courseId)
        ->first();
        if(!$enrollment){
            return response()->json([  
                'status'=>false,
                'message'=>'Enrollment not found'
            ],404); 
        }
        $enrollment->delete();
        return response()->json([  
            'status'=>true,
            'message'=>'Unenrolled successfully'
        ]);
    }
}
