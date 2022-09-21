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

}
