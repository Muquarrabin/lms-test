<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\ModuleModel;
use App\Models\Course\CategoryModel;

class CourseModel extends Model
{
    use HasFactory;
    protected $table="courses";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'category_id'
    ];

    //relations
    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id', 'id');
    }
    public function modules()
    {
        return $this->hasMany(ModuleModel::class, 'course_id', 'id');
    }
}
