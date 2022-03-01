<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CategoryResource;
use App\Models\Course\CategoryModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        $categories=CategoryModel::with('courses')->get();
        return CategoryResource::collection($categories)->additional(
            [
                'success'=>true,
                'message'=>'All Category Data with Courses'
            ]
        );;
    }
}
