<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\CourseModel;
use App\Models\Course\UnitModel;


class ModuleModel extends Model
{
    use HasFactory;
    protected $table="modules";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'course_id'
    ];

    //relations
    public function course()
    {
        return $this->belongsTo(CourseModel::class, 'course_id', 'id');
    }
    public function units()
    {
        return $this->hasMany(UnitModel::class, 'module_id', 'id');
    }
}
