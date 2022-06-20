<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

class PhotoHandler
{
    /**
     * Method Upload image using Intervention
     * @param $imageName        String custom image name
     * @param $imagePath        String image location in directory
     * @param $imageFile        File Image
     * @param $aspectRatio      String image ration | optional
     * @return void
     * @author Sopha Pum | 28-05-2021
     */
    public static function uploadImage($imgName, $imagePath, $imageFile, $aspectRatio=""){ 
        //setup aspect ratio size, ref: https://calculateaspectratio.com/
        $aspectRatioMapping = [
                                "1:1" => ["thumb" => ["w" => 256, "h" => 256],
                                        "normal" => ["w" => 1080, "h" => 1080]],
                                "1:2" => ["thumb" => ["w" => 256, "h" => 512],
                                        "normal" => ["w" => 1080, "h" => 2160]],   
                                "2:3" => ["thumb" => ["w" => 256, "h" => 384],
                                        "normal" => ["w" => 1080, "h" => 1620]],   
                                "3:2" => ["thumb" => ["w" => 258, "h" => 172],
                                        "normal" => ["w" => 1080, "h" => 720]],
                                "3:4" => ["thumb" => ["w" => 258, "h" => 344],
                                        "normal" => ["w" => 1080, "h" => 1440]],
                                "4:3" => ["thumb" => ["w" => 256, "h" => 192],
                                        "normal" => ["w" => 1280, "h" => 960]],
                                "4:5" => ["thumb" => ["w" => 256, "h" => 320],
                                        "normal" => ["w" => 1280, "h" => 1600]],
                                "9:16" => ["thumb" => ["w" => 256, "h" => 455],
                                        "normal" => ["w" => 1080, "h" => 1920]],
                                "16:9" => ["thumb" => ["w" => 256, "h" => 144],
                                        "normal" => ["w" => 1280, "h" => 720]],
                            ];
        // return \response()->json($aspectRatioMapping);
        //default ratio full image 
        $activeRatio = $aspectRatio != "" && isset($aspectRatioMapping[$aspectRatio]) ? $aspectRatioMapping[$aspectRatio] : "";
        $imgExt = $imageFile->getClientOriginalExtension();

        $imageName = $imgName . '.' . $imgExt;
        $imageThumbName = $imgName . '-thumb.' . $imgExt; 
        
        //create directory if not existed
        $imageDist = 'galleries' . $imagePath;
        if(!file_exists($imageDist)){
            mkdir($imageDist, 0777, true);
        }

        $imgManager = new ImageManager(["driver" => 'imagick']);

        $activeImage = $imgManager->make($imageFile->getRealPath());
        $width = $activeImage->width();
        $thumbWidth = $activeImage->width();
        $height = $activeImage->height();
        $thumbHeight = $activeImage->height();

        if($activeRatio != ""){
            $width = $activeRatio["normal"]["w"];
            $thumbWidth = $activeRatio["thumb"]["w"];
            $height = $activeRatio["normal"]["h"];
            $thumbHeight = $activeRatio["thumb"]["h"];
        }
 
        //create thumbnail image
        $imgThumb = $imgManager->make($imageFile->getRealPath());
        $imgThumb->fit($thumbWidth, $thumbHeight, function($constraint){ 
            $constraint->upsize();
        })->save($imageDist.'/'.$imageThumbName);

        //create large image
        $img = $imgManager->make($imageFile->getRealPath());
        $img->fit($width, $height, function($constraint){ 
            $constraint->upsize();
        })->save($imageDist.'/'.$imageName);

        
    }
}
