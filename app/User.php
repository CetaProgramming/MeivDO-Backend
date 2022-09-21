<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;

    public function role(){
        return $this->belongsTo('App\Role');
    }
    public function groupTools(){
        return $this->hasMany('App\groupTool','category_tools_id','id');
    }
    public function categoryTools(){
        return $this->hasMany('App\categoryTool');
    }
    public function tools(){
        return $this->hasMany('App\Tool');
    }
    public function project(){
        return $this->hasMany('App\Project');
    }
    public function projectTools(){
        return $this->hasMany('App\ProjectTool');
    }
    protected static function booted()
    {
        static::deleting(function ($goalType) {
            if ($goalType->groupTools()->exists() ||$goalType->categoryTools()->exists()||$goalType->tools()->exists()||$goalType->Project()->exists()||$goalType->ProjectTools()->exists()) {
                throw new \Exception("The user have relations", 500);
            }
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','active','role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    use SoftDeletes;
}
