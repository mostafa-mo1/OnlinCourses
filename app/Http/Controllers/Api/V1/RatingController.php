<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Rating;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $course=Course::with('ratings.user')->find($courseId);
        if(!$course){
            return response()->json([
            'status'=>false,         
            'message'=>'Course not found'
        ],404); 
        return response()->json([
            'status'=>true,
            'data'=>$course->ratings
        ]);
        }}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$courseId)
    {
        $user=auth()->user();
        $request->validate([
            'rating'=>'required|integer|min:1|max:5',
        ]);
        $course=Course::find($courseId);
        if(!$course){
            return response()->json([  'status'=>false,
            'message'=>'Course not found'
        ],404);
       }
       //prevent instructor from rating own course
       if($course->user_id ===$user->id){
        return response()->json([  
            'status'=>false,
            'message'=>'You cannot rate your own course'
        ],403);
        //create or update rating
        $rating=Rating::updateOrCreate([
            'user_id'=>$user->id,
            'course_id'=>$courseId,
        ],[
            'rating'=>$request->rating,
        ]);
        $avrage=Rating::where('course_id',$courseId)->avg('rating');
        $course->update([
            'average_rating'=>$avrage,
        ]);
        return response()->json([
            'status'=>true,
            'data'=>$rating
        ],201);
    }
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
        //did in store method
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseId)
    {
        $user=auth()->user();
        $rating=Rating::where('user_id',$user->id)->where('course_id',$courseId)->first();
        if(!$rating){
            return response()->json([ 
            'status'=>false,
            'message'=>'Rating not found'
        ],404); 
        }
        if($rating->user_id !==$user->id){
            return response()->json([  
            'status'=>false,
            'message'=>'You are not authorized to delete this rating'
        ],403); 
        }
        $rating->delete();
        $average=Rating::where('course_id',$courseId)->avg('rating');
        $course=Course::find($courseId);
        $course->update([
            'average_rating'=>$average,
        ]);
        return response()->json([  
            'status'=>true,
            'message'=>'Rating deleted successfully'
        ]);
    }
}
