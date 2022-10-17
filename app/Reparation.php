<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reparation extends Model
{
    protected $fillable = ['tool_id','project_id','user_id'];
    use SoftDeletes;
}
