<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categoryTool extends Model
{
    protected $fillable = ['name'];
    use SoftDeletes;

}
