<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use Asm89\Stack\Cors;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses = Course::with('category','instructor','lessons','enrollments');
        // search by title or description\\\\\
        if($request->has('search')){
            $search=$request->search;
            $courses->where(function($query) use ($search){
                $query->where('title','like',"%$search%")
                      ->orWhere('description','like',"%$search%");
            });
        }
        // filter by category \\\\
        if ($request->has('category')){
            $courses->whereHas('category',function($query) use ($request){
                $query->where('name',$request->category);
            });
        }
        /////// sorting \\\\
        if ($request->has('sort_by')) {
        switch ($request->sort_by) {
            case 'latest':
                $courses->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $courses->orderBy('created_at', 'asc');
                break;
            case 'title_asc':       
                $courses->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $courses->orderBy('title', 'desc');
                break;
           
           }
        }else{
            $courses->latest();    
        

        }
                

        
        // if ($request->has('rating')){
        //    $courses->where('average_rating','>=',$request->rating);
        // }


        //pagination\\\\\
        $perpage=$request->get('per_page',3);
        $courses = $courses->paginate($perpage);
        return response()->json([
            'status'=>true,
            'data'=>CourseResource::collection($courses),
            'pagination'=>[
                'total'=>$courses->total(),
                'per_page'=>$courses->perPage(),
                'current_page'=>$courses->currentPage(),
                'last_page'=>$courses->lastPage(),
                'has_more_pages' => $courses->hasMorePages(),
                'from'=>$courses->firstItem(),
                'to'=>$courses->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if(Auth::user()->role !='instructor'){
        //     return response()->json([  'status'=>false,'message'=>'Only instructors can create courses'],403);
        // }
        $this->authorize('create', Course::class);
        $data=$request->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
            'category_id'=>'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($request->hasFile('image')) {
        $path = $request->file('image')->store('courses', 'public');
        $validated['image'] = $path;
        }
        $course=Course::create([
            'title'=>$data['title'],
            'description'=>$data['description'],
            'category_id'=>$data['category_id'],
            'user_id'=> Auth::id(),
            'image'=>isset($data['image']) ? $data['image']->store('courses','public') : null,
        ]);
        return new CourseResource($course);
        // return response()->json([  'status'=>true,'data'=>$course],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $corses=Course::with('category','instructor','lessons','ratings','comments')->find($id);
        if(!$corses){
            return response()->json([  'status'=>false,'message'=>'Course not found'],404);
        }
        return response()->json([  'status'=>true,'data'=>$corses]);
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
        $course=Course::find($id);
        if(!$course){
            return response()->json([  'status'=>false,'message'=>'Course not found'],404);
            // if(Auth::id() !=$course->user_id){
            //     return response()->json([  'status'=>false,'message'=>'Unauthorized'],403);
            // }
            //this will check policy 
            $this->authorize('update', Course::class);
        }
        $data=$request->validate([
            'title'=>'sometimes|string|max:255',
            'description'=>'sometimes|string',
            'category_id'=>'sometimes|exists:categories,id',
            'image'=>'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
        $path = $request->file('image')->store('courses', 'public');
        $validated['image'] = $path;
        }
        $course->update($data);
        if(isset($data['image'])){
            $course->image=$data['image']->store('courses','public');
            $course->save();
        }
        return response()->json([  'status'=>true,'data'=>$course]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course=Course::find($id);
        if(!$course){
            return response()->json([  'status'=>false,'message'=>'Course not found'],404);
        }
        // if(Auth::id() !=$course->user_id){
        //     return response()->json([  'status'=>false,'message'=>'Unauthorized'],403);
        // }
        //this will check policy 
        $this->authorize('delete', '$course');
        $course->delete();
        return response()->json([  'status'=>true,'message'=>'Course deleted successfully']);
    }
}
