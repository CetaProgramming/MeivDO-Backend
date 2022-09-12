<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUpload
{
    static public function saveImage($request,$dataBaseName,$row){

        if ($request->file('image')) {
            $imagePath = $request->file('image');
            $imageName =  Str::of($imagePath->getClientOriginalName())->split('/[\s.]+/');
            Storage::deleteDirectory('public/images/'.$dataBaseName.'/'. $row->id);
            return $request->file('image')->storeAs('images/'.$dataBaseName.'/'. $row->id,$row->id."_profile.". $imageName[1], 'public');
        }


    }
}
