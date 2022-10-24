<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ProjectTool;
class Project extends Model
{
    protected $fillable = ['name','address','status','startDate','endDate'];
    use SoftDeletes;
    public function projectTools(){
        return $this->hasMany('App\ProjectTool')->with('tool');
    }
    public function user(){
        return $this->belongsTo('App\User');

    }
    protected static function booted()
    {
        static::deleting(function ($goalType) {
            if ($goalType->status ==0) {
                throw new \Exception("The project is close", 500);
            }
        });
    }
}
