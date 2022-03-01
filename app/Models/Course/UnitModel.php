<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\ModuleModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UnitModel extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $table="units";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'module_id'
    ];

    /**
     * defaining media/file collection
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        //files
        $this->addMediaCollection('unit_files')->singleFile();
    }
    //relations

    public function module()
    {
        return $this->belongsTo(ModuleModel::class, 'module_id', 'id');
    }

}
