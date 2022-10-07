<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class groupTool extends Model
{

    protected $fillable = ['code','image','active','description'];
    use SoftDeletes;

    public function categoryTools(){
        return $this->belongsTo('App\categoryTool');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function tools(){
        return $this->hasMany('App\Tool','group_tools_id','id');
    }
    protected static function booted()
    {
        static::deleting(function ($goalType) {

            if ($goalType->tools()->get()->count()>0) {
                throw new \Exception("The groupTool have relations", 500);
            }
        });
    }
}
