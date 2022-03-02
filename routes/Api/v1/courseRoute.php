<?php

use App\Http\Controllers\Course\CategoryController;
use App\Http\Controllers\Course\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'course', 'middleware' => ['auth:sanctum']], function () {

    Route::group(['middleware' => ['role:student']], function () {
       Route::get('/get-categories',[CategoryController::class,'getAllCategories']);
    });
       Route::get('/get-courses',[CourseController::class,'getCoursesPaginated']);
       Route::post('/get-single-course',[CourseController::class,'getCourseById']);
});
