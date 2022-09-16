<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Active
{
    static public function verifyActive($data){
        if($data->active==0){
            Log::info("{$data->getTable()} with id: {$data->id} is not active");
            throw new \Exception("{$data->getTable()} with id: {$data->id} is not active", 500);
        }

    }
}
