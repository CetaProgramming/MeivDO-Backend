<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class IsDeleted
{
    static public function verifyDeleted($data){
        if($data!= null){
            Log::info("Message: {$data->getTable()} with id: {$data->id} doesn't exist");
            throw new \Exception("{$data->getTable()} with id: {$data->id} doesn't exist", 500);
        }

    }
}
