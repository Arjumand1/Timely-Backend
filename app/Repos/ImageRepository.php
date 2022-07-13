<?php
namespace App\Repos;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageRepository
{
  // define a method to upload our image
    public function upload_image($base64_image,$image_path){
        //The base64 encoded image data
        $image_64 = $base64_image;
        // exploed the image to get the extension
        $extension = explode(';base64',$image_64);
        //from the first element
        $extension = explode('/',$extension[0]);
        // from the 2nd element
        $extension = $extension[0];

        $replace = substr($image_64, 0, strpos($image_64, ',')+1);

        // finding the substring from
        // replace here for example in our case: data:image/png;base64,
        $image = str_replace($replace, '', $image_64);
        // replace
        $image = str_replace(' ', '+', $image);
        // set the image name using the time and a random string plus
        // an extension
        $imageName = time().'_'.Str::random(20).$extension;
        // save the image in the image path we passed from the
        // function parameter.
        Storage::disk('public')->put($image_path .$imageName, base64_decode($image));
        // return the image path and feed to the function that requests it
        return $image_path.$imageName;
    }
}
