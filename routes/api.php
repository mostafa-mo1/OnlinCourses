<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Notifications\StudentEnrolledNotification;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile',[AuthController::class,'profile']);
    });
}); 
Route::group([
    'prefix' => 'v1',
   'middleware' => 'auth:sanctum'
], function () {
    Route::apiResource('/courses', CourseController::class);
    Route::get('courses/{course}/lessons', [LessonController::class, 'index']);
    Route::post('courses/{course}/lessons', [LessonController::class, 'store']);
    Route::get('courses/{course}/lessons/{lesson}', [LessonController::class, 'show']);
    Route::put('courses/{course}/lessons/{lesson}', [LessonController::class, 'update']);
    Route::delete('courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy']);
    ////Enrollment Routes////
    Route::get('enrollments', [EnrollmentController::class, 'index']); //courses is enrolled by user
    Route::post('courses/{course}/enroll', [EnrollmentController::class, 'store']); //enroll in a course
    Route::delete('courses/{course}/unenroll', [EnrollmentController::class, 'destroy']); //enroll from a course
    ////Comment Routes////
    Route::get('courses/{course}/comments', [CommentController::class, 'index']); //get comments for a course
    Route::post('courses/{course}/comments', [CommentController::class, 'store']); //add comment to a course 
    Route::put('courses/{course}/comments/{comment}', [CommentController::class, 'update']); //update comment from a course
    Route::delete('courses/{course}/comments/{comment}', [CommentController::class, 'destroy']); //delete comment from a course
    ///Rating Routes////
    Route::get('courses/{course}/ratings', [App\Http\Controllers\Api\V1\RatingController::class, 'index']); //get ratings for a course
    Route::post('courses/{course}/ratings', [App\Http\Controllers\Api\V1\RatingController::class, 'store']); //add rating & update to a course 
    Route::delete('courses/{course}/ratings/{rating}', [App\Http\Controllers\Api\V1\RatingController::class, 'destroy']); //delete rating from a course
    //Notification Routes////
    Route::get('/notifications', function () {
    return auth()->user()->notifications;
   });
   //dashboard route
   Route::get('/dashboard', [App\Http\Controllers\Api\V1\DashboardController::class, 'index']);
   
    
});
