<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Lesson;
class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $course = Course::with('lessons')->findOrFail($courseId);
        return response()->json($course->lessons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $courseId)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|integer',
            'course_id' => 'required|exists:courses,id',
        ]);
        $course = Course::findOrFail($courseId);
        if (auth()->id() != $course->user_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }
        $lesson = Lesson::create([
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'course_id' => $courseId,
        ]);
        return LessonResource::make($lesson);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($courseId, $lessonId)
    {
        //find the lisson that courseid and lessonid match
        $lesson = Lesson::where('course_id', $courseId)->where('id', $lessonId)->firstOrFail();
        return LessonResource::make($lesson);
        // return response()->json([
        // 'status' => true,
        // 'data' => $lesson
    // ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $courseId, $lessonId)
    {
        $lisson=Lesson::where('course_id',$courseId)->where('id',$lessonId)->firstOrFail();
        if(auth()->id() !=$lisson->course->user_id){
            return response()->json([  'status'=>false,'message'=>'Unauthorized'],403);
        }
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|nullable|string',
            'video_url' => 'sometimes|nullable|integer',
        ]);
        $lisson->update($data);
        return response()->json([  'status'=>true,'data'=>$lisson]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseId, $lessonId)
    {
        $lesson=Lesson::where('course_id',$courseId)->where('id',$lessonId)->firstOrFail();
        if(auth()->id() !=$lesson->course->user_id){
            return response()->json([  'status'=>false,'message'=>'Unauthorized'],403); 
        }
        $lesson->delete();
        return response()->json([  'status'=>true,'message'=>'Lesson deleted successfully']);
    }
}
