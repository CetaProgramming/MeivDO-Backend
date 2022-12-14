<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categoryTool extends Model
{
    protected $fillable = ['name','active'];
    use SoftDeletes;

    public function groupTools(){
        return $this->hasMany('App\groupTool','category_tools_id','id');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    protected static function booted()
    {
        static::deleting(function ($goalType) {
            if ($goalType->groupTools()->exists()) {
                throw new \Exception("The category have relations", 500);
            }
        });
    }
}
