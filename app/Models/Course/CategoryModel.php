<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\CourseModel;


class CategoryModel extends Model
{
    use HasFactory;

    protected $table="categories";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];

    //relations
    public function courses()
    {
        return $this->hasMany(CourseModel::class, 'category_id', 'id');
    }
}
