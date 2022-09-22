<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTool extends Model
{
     protected $fillable = ['tool_id','project_id','user_id'];
    use SoftDeletes;
    public function tool(){
        return $this->belongsTo('App\Tool');
    }
    public function project(){
        return $this->belongsTo('App\Project');
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
        static::creating(function ($goalType) {
            if (self::where('tool_id', $goalType->tool_id)->where('project_id', $goalType->project_id)->exists()) {
                throw new \Exception("The project tools id has duplications", 500);
            }
        });
        static::updating(function ($goalType) {
            if (self::where('tool_id', $goalType->tool_id)->where('project_id', $goalType->project_id)->exists()) {
                throw new \Exception("The project tools id has duplications", 500);
            }
        });
    }
}
