<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class groupTool extends Model
{

    protected $fillable = ['code','image','category_id','description'];
    use SoftDeletes;

    public function categoryTools(){
        return $this->belongsTo('App\categoryTool');
    }
}
