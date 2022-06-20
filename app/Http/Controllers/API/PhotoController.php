<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RestResource;
use App\Model\Photos;
use App\Model\Post;
use App\Model\Products;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager; 


class PhotoController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'photos',
            'model' => 'App\Model\Photos', 
            'prefixId' => 'photo'
        ];
    }
    
    public function getQuery(){
        return Photos::query();
    }
    
    public function getModel(){
        return 'App\Model\Photos';
    }

    public function getCreateRules(){
        return [
            // "photo" => "required"
        ];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }

    /**
     * Method upload image from frontend 
     * @param $request  Request data
     *                      required: 
     *                          path: string file path, 
     *                          name: string file image name, 
     *                          file: file iamge
     *                      optional: 
     *                          aspectRatio: image size default 16:9 
     *                                      (1:1, 1:2, 2:3, 3:2, 3:4, 4:3, 4:5, 9:16, 16:9)
     *                                
     * @return JSON result
     * @author Sopha Pum
     */
    public function uploadImage(Request $request){
 
 
        // $input = $request->all();
        // return $request->name . ', path: ' 
        //         . $request->path . ', has file:' 
        //         . ($request->hasFile('file') ? "true" : "false"); 
        
        //setup aspect ratio size, ref: https://calculateaspectratio.com/

        $aspectRatioMapping = [
                                "1:1" => ["thumb" => ["w" => 600, "h" => 600],
                                        "normal" => ["w" => 1080, "h" => 1080]],
                                "1:2" => ["thumb" => ["w" => 600, "h" => 1200],
                                        "normal" => ["w" => 1080, "h" => 2160]],   
                                "2:3" => ["thumb" => ["w" => 600, "h" => 900],
                                        "normal" => ["w" => 1080, "h" => 1620]],   
                                "3:2" => ["thumb" => ["w" => 600, "h" => 400],
                                        "normal" => ["w" => 1080, "h" => 720]],
                                "3:4" => ["thumb" => ["w" => 600, "h" => 800],
                                        "normal" => ["w" => 1080, "h" => 1440]],
                                "4:3" => ["thumb" => ["w" => 600, "h" => 450],
                                        "normal" => ["w" => 2048, "h" => 1536]],
                                "4:5" => ["thumb" => ["w" => 600, "h" => 750],
                                        "normal" => ["w" => 1280, "h" => 1600]],
                                "9:16" => ["thumb" => ["w" => 603, "h" => 1072],
                                        "normal" => ["w" => 1152, "h" => 2048]],
                                "16:9" => ["thumb" => ["w" => 608, "h" => 342],
                                        "normal" => ["w" => 2048, "h" => 1152]],
                            ];
        // return \response()->json($aspectRatioMapping);
        //default original size
        $requestRatio = isset($request->aspectRatio) && !empty($request->aspectRatio) ? $request->aspectRatio : "";
        $activeRatio = isset($aspectRatioMapping[$requestRatio]) ? $aspectRatioMapping[$requestRatio] : "";

        $image = $request->file("file"); 
        $imageName = $request->name . '.' . $image->getClientOriginalExtension();
        $imageThumbName = $request->name . '-thumb.' . $image->getClientOriginalExtension();
        $imageDist = 'galleries'. (empty($request->path) ? '' : '/'. $request->path);
        
        //create directory if not existed
        if(!file_exists($imageDist)){
            mkdir($imageDist, 0777, true);
        }

        // $imgManager = new ImageManager(new array(["driver" => 'imagick']));
 
        $activeImage = Image::make($image->getRealPath());
        $width = $activeImage->width();
        $height = $activeImage->height();
        
        $thumbWidth = $activeImage->width();
        $thumbHeight = $activeImage->height();

        if($activeRatio != ""){
            $width = $activeRatio["normal"]["w"];
            $thumbWidth = $activeRatio["thumb"]["w"];
            $height = $activeRatio["normal"]["h"];
            $thumbHeight = $activeRatio["thumb"]["h"];
        }
 
        //create thumbnail image
        $imgThumb = Image::make($image->getRealPath());
        $imgThumb->fit($thumbWidth, $thumbHeight, function($constraint){ 
            $constraint->upsize();
        })->save($imageDist.'/'.$imageThumbName);

        //create large image
        $img = Image::make($image->getRealPath());
        $img->fit($width, $height, function($constraint){  
            $constraint->upsize();
        })->sharpen(15)->save($imageDist.'/'.$imageName, 90);

        // $img->resize($width, null, function($constraint){ 
        //     $constraint->aspectRatio();
        // })->save($imageDist.'/'.$imageName, 100);
  
        return \response()->json([
            "message" => "File successfully uploaded"
        ]);
    }

    public function publicImageGallery(Request $request){
        try{
             
            $lstFilter = $request->all();

            $lstProductPhotos = [];
            $lstPostPhotos = [];

            //get photo from post
            if(!isset($lstFilter['post']) || (isset($lstFilter['post']) && $lstFilter['post'] == 1)){
                $lstPosts = Post::where("is_active", 1)
                                ->where("status", "publised")
                                ->whereNotNull("main_photo")
                                ->get()
                                ->toArray();

                //convert product object to photo
                foreach ($lstPosts as $key => $post) {
                    $lstPostPhotos[] = [
                        "photo" => $post["main_photo"],
                        "photo_preview" => $post["main_photo"],
                        "record_type_name" => $post["record_type"],
                        "is_pano" => 0
                    ];
                }
            }

            //get photo from product
            if(!isset($lstFilter['product']) || (isset($lstFilter['product']) && $lstFilter['product'] == 1)){
                $lstProducts = Products::where("is_active", 1)
                                        ->whereNotNull("photo")
                                        ->get()
                                        ->toArray();

                //convert product object to photo
                foreach ($lstProducts as $key => $prod) {
                    $lstProductPhotos[] = [
                        "photo" => $prod["photo"],
                        "photo_preview" => $prod["photo"],
                        "record_type_name" => $prod["record_type_name"],
                        "is_pano" => 0
                    ];
                }
            }

            $lstPhotos = Photos::whereNotNull("photo")->get()->toArray();

            
            return new RestResource(collect(array_merge($lstPhotos, $lstProductPhotos, $lstPostPhotos))); 
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
