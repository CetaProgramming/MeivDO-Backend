<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTool extends Model
{
    // protected $fillable = ['name','active'];
    use SoftDeletes;
    public function tool(){
        return $this->belongsTo('App\Tool');
    }
    public function project(){
        return $this->hasOne('App\Project');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    protected static function booted()
    {
        static::deleting(function ($goalType) {
            if ($goalType->tool()->exists()||$goalType->project()->exists()) {
                throw new \Exception("The project tools have relations", 500);
            }
        });
    }
}
