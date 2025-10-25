<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\LessonResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $course=Course::with('comments.user')->find($courseId);
        if(!$course){
            return response()->json([  'status'=>false,
            'message'=>'Course not found'
        ],404); 
        }
        return CommentResource::collection($course->comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $courseId)
    {
        $user=auth()->user();
        $request->validate([
            'content'=>'required|string|max:1000',
        ]);
        $course=Course::find($courseId);
        if(!$course){
            return response()->json([  'status'=>false,
            'message'=>'Course not found'
        ],404); 
        }
        $comment=Comment::create([
            'user_id'=>$user->id,
            'course_id'=>$courseId,
            'content'=>$request->content,
        ]);
        // Notify the course instructor about the new comment
        $instructor=$course->instructor;
        if($instructor && $instructor->id !==$user->id){
            $instructor->notify(new \App\Notifications\NewCommentNotification($course,$comment));
        }
        return response()->json([
            'status'=>true,
            'data'=>$comment
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
        
        // $comment=Comment::find($id);
        // if(!$comment){
        //     return response()->json([  'status'=>false,
        //     'message'=>'Comment not found'
        // ],404); 
        // }
        // return CommentResource::make($comment);
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
        $user=auth()->user();
        $comment=Comment::find($id);
        if(!$comment){
            return response()->json([  'status'=>false,
            'message'=>'Comment not found'
        ],404); 
        }
        if($comment->user_id !==$user->id){
            return response()->json([  'status'=>false,
            'message'=>'You are not authorized to update this comment'
        ],403); 
        }
        $request->validate([
            'content'=>'required|string|max:1000',
        ]);
        $comment->content=$request->content;
        $comment->save();
        return response()->json([
            'status'=>true,
            'data'=>$comment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=auth()->user();
        $comment=Comment::find($id);
        if(!$comment){
            return response()->json([  'status'=>false,
            'message'=>'Comment not found'
        ],404); 
        }
        if($comment->user_id !==$user->id){
            return response()->json([  'status'=>false,
            'message'=>'You are not authorized to delete this comment'
        ],403); 
        }
        $comment->delete();
        return response()->json([
            'status'=>true,
            'message'=>'Comment deleted successfully'
        ]);

    }
}
