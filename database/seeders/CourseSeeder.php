<?php

namespace Database\Seeders;

use App\Models\Course\CategoryModel;
use App\Models\Course\CourseModel;
use App\Models\Course\ModuleModel;
use App\Models\Course\UnitModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();
            for ($i = 0; $i < 5; $i++) {
                $random_string = Str::random(15);

                $category = CategoryModel::create(
                    [
                        'title' => $random_string,
                    ]
                );
                $course = CourseModel::create(
                    [
                        'title' => $random_string,
                        'description' => $random_string,
                        'category_id' => $category->id,
                    ]
                );
                $module = ModuleModel::create(
                    [
                        'title' => $random_string,
                        'course_id' => $course->id,
                    ]
                );
                $unit = UnitModel::create(
                    [
                        'title' => $random_string,
                        'module_id' => $module->id,
                    ]
                );
                $url = 'https://helpx.adobe.com/content/dam/help/en/photoshop/using/convert-color-image-black-white/jcr_content/main-pars/before_and_after/image-before/Landscape-Color.jpg';
                $unit->addMediaFromUrl($url)
                    ->toMediaCollection();
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
