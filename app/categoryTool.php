<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categoryTool extends Model
{
    protected $fillable = ['name'];
    use SoftDeletes;
    public function groupTools(){
        return $this->hasMany('App\groupTool');
    }
}
