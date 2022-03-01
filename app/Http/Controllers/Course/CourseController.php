<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Models\Course\CourseModel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
class CourseController extends Controller
{
    public function getCoursesPaginated(Request $request)
    {
        $courses=CourseModel::all();
        $page_no=$request->page;
        $per_page=$request->filled("per_page") ? $request->per_page : 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $paginatedItems = new LengthAwarePaginator(
                $courses->forPage($currentPage, $per_page),
                $courses->count(),
                $per_page,
                $currentPage,
                ['path' => url('api/course/get-courses')]
            );
            return CourseResource::collection($paginatedItems)->additional(
                [
                    'success'=>true,
                    'message'=>'All Course Data'
                ]
            );
    }
    public function getCourseById(Request $request)
    {
        $course=CourseModel::with(['modules.units.media'])->find($request->id);
        return CourseResource::make($course)->additional(
            [
                'success'=>true,
                'message'=>'Specific Course Data'
            ]
        );
    }
}
