<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

class FileUploadController extends Controller
{

    public function validation($lstRecords, $rules=[], $customMsg=[]){
        if (empty($rules)) return;
 
        foreach ($lstRecords as $index => $record) {
            $validate = Validator::make($record, $rules, $customMsg);
            if ($validate->fails()) {
                throw new CustomException($validate->errors()->first(), 
                                            CustomException::$INVALID_FIELD, 
                                            $validate->errors()); 
            }
        }
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

        // $this->validation([$request->all()], [
        //     "path" => "required",
        //     "name" => "required"
        //     // "file" => "required|mimes:jpeg,png,jpg,gif,svg"
        // ]); 
 
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
                                        "normal" => ["w" => 1280, "h" => 960]],
                                "4:5" => ["thumb" => ["w" => 600, "h" => 750],
                                        "normal" => ["w" => 1280, "h" => 1600]],
                                "9:16" => ["thumb" => ["w" => 603, "h" => 1072],
                                        "normal" => ["w" => 1080, "h" => 1920]],
                                "16:9" => ["thumb" => ["w" => 608, "h" => 342],
                                        "normal" => ["w" => 1280, "h" => 720]],
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

        $imgManager = new ImageManager(["driver" => 'imagick']);

        $activeImage = $imgManager->make($image->getRealPath());
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
        $imgThumb = $imgManager->make($image->getRealPath());
        $imgThumb->fit($thumbWidth, $thumbHeight, function($constraint){ 
            $constraint->upsize();
        })->save($imageDist.'/'.$imageThumbName);

        //create large image
        $img = $imgManager->make($image->getRealPath());
        $img->fit($width, $height, function($constraint){ 
            $constraint->upsize();
        })->save($imageDist.'/'.$imageName);
  
        return \response()->json([
            "message" => "File successfully uploaded"
        ]);
    }

    /**
     * Method upload image from frontend 
     * @param $request  Request data
     *                      required: 
     *                          path: string file path, 
     *                          name: string file image name, 
     *                          file: file iamge
     *                                
     * @return JSON result
     * @author Sopha Pum
     */
    public function uploadPDF(Request $request){

        $this->validation([$request->all()], [
            "path" => "required",
            "name" => "required",
            "file" => "required|mimes:pdf|max:20480"
        ]); 
     
        if($request->hasFile("file")){
            $pdfFile = $request->file("file"); 
            $filename = $request->name; 
            $fileExtension = $pdfFile->getClientOriginalExtension();
            $fileDist = 'documents'. (empty($request->path) ? '' : '/'. $request->path);
            
            //create directory if not existed
            if(!file_exists($fileDist)){
                mkdir($fileDist, 0777, true);
            }
    
            $request->file('file')->move($fileDist, $filename . '.' . $fileExtension); 

            return \response()->json([
                "message" => "File successfully uploaded"
            ]);
        }else{
            return \response()->json([
                "message" => "No file to upload."
            ]);
        }
        
    }


    public function uploadVideo(Request $request){

        $this->validation([$request->all()], [
            "path" => "required",
            "name" => "required",
            "file" => "required|mimes:mp4|max:20480"
        ]); 
     
        if($request->hasFile("file")){
            $pdfFile = $request->file("file"); 
            $filename = $request->name; 
            $fileExtension = $pdfFile->getClientOriginalExtension();
            $fileDist = 'videos'. (empty($request->path) ? '' : '/'. $request->path);
            
            //create directory if not existed
            if(!file_exists($fileDist)){
                mkdir($fileDist, 0777, true);
            }
    
            $request->file('file')->move($fileDist, $filename . '.' . $fileExtension); 

            return \response()->json([
                "message" => "Video successfully uploaded"
            ]);
        }else{
            return \response()->json([
                "message" => "No file to upload."
            ]);
        }
        
    }
    
}
