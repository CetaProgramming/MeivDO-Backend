<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
   // protected $fillable = ['name','active'];
    use SoftDeletes;
    public function projectTools(){
        return $this->hasMany('App\ProjectTool');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    protected static function booted()
    {
        static::deleting(function ($goalType) {
            if ($goalType->projectTools()->exists()) {
                throw new \Exception("The project have relations", 500);
            }
        });
    }
}
