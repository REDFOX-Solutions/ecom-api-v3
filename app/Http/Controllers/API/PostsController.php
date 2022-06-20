<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Post;
use App\Http\Resources\RestResource; 
use App\Services\DatabaseGW;
use App\Model\Categories;
use Illuminate\Support\Facades\Input;

class PostsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'posts',
            'model' => 'App\Model\Post',
            'modelTranslate' => 'App\Model\PostTranslation',
            'prefixId' => 'arc',
            'prefixLangId' => 'arc0t',
            'parent_id' => 'posts_id'
        ];
    }
    
    public function getQuery(){
        return Post::query();
    }
    
    public function getModel(){
        return 'App\Model\Post';
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }

    // trigger handler
    public function beforeCreate(&$lstNewRecords){
        $today = date('Y-m-d\TH:i:sP'); 
        foreach ($lstNewRecords as $colName => &$value) {
            if(isset($value['status']) && $value['status'] == 'published' && !isset($value['posted_at'])){
                $value['posted_at'] = $today;
            }
        }
    }
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        $today = date('Y-m-d\TH:i:sP'); 
        foreach ($lstNewRecords as $colName => &$value) {
            $oldRecord = $mapOldRecords[$value['id']];

            if(isset($value['status']) && 
                $oldRecord['status'] != $value['status'] && 
                $value['status'] == 'published' && 
                !isset($value['posted_at']))
            {
                $value['posted_at'] = $today;
            }
        }
    }

    public function showBySlug($slug){
        $record = Post::query()->where("slug", $slug)->where("status", "published")->with("author")->firstOrFail();
        return new RestResource($record);
    }

    public function showGuestPost(){
        try{
            $model = $this->getQuery();
            $filters = ["status" => "publish", "with"=>"author", "order_col"=>"created_date", "order_by"=>"desc"];
            $lstRecords = DatabaseGW::queryByModel($model, $filters);
            return RestResource::collection($lstRecords);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    //guest access
    public function publicIndex(Request $request){
        try{
            $lstFilter = $request->all();
            $lstFilter['status'] = 'published';
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function publicSearch(Request $request){
        try{
            $lstFilter = $request->all();
             
            //get all category and put it as map by key is slud and value is id
            $cateKeyed = Categories::get()->mapWithKeys(function($item){
                return [$item['slug'] => $item["id"]];
            });
            $mapCategory = $cateKeyed->all();
 
            $cateExclude = [$mapCategory['about-h-e'], 
                            $mapCategory['about-kskns'], 
                            $mapCategory['about-office']];

            $query = Post::where('status', 'published');
            // $query->whereNotIn('category_ids', $cateExclude);

            
            //search article by keyword with title and short desc
            if(isset($lstFilter["keyword"])){

                $keyword = $lstFilter["keyword"];

                $query->whereHas('langs', function($query) use ($keyword){
                    $query->where('title', 'like', "%$keyword%")->orWhere('short_desc', 'like', "%$keyword%");
                });

                $cateSelected = [];

                //check article type first,
                // if there are no specific article type, we will get all
                if(isset($lstFilter['type_activity']) && $lstFilter['type_activity'] == 1){
                    $cateSelected[] = $mapCategory['activity-news'];
                }

                if(isset($lstFilter['type_nanews']) && $lstFilter['type_nanews'] == 1){
                    $cateSelected[] = $mapCategory['na-news'];
                }

                if(isset($lstFilter['type_press_release']) && $lstFilter['type_press_release'] == 1){
                    $cateSelected[] = $mapCategory['press-release'];
                }

                if(isset($lstFilter['type_public_notice']) && $lstFilter['type_public_notice'] == 1){
                    $cateSelected[] = $mapCategory['public-notice'];
                }

                if(isset($lstFilter['type_speech']) && $lstFilter['type_speech'] == 1){
                    $cateSelected[] = $mapCategory['speech'];
                }

                if(isset($lstFilter['video']) && $lstFilter['video'] == 1){
                    $cateSelected[] = $mapCategory['videos'];
                }

                if(isset($cateSelected) && count($cateSelected) > 0){
                    $query->whereIn('category_ids', $cateSelected);
                } 
                
            }            

            //filter by date
            if(isset($lstFilter['posted'])){
                $query->whereDate('posted_at', $lstFilter['posted']);
            }

            
            //filter by doc type
            if(isset($lstFilter['image']) && $lstFilter['image'] == 1){
                $query->has("photos", '>', 0);
            }
            
            if(isset($lstFilter['attachment']) && $lstFilter['attachment']==1){
                $query->has('documents', '>', 0);
            }
            $query->with("updatedBy");
            $query->orderBy('created_date', "desc");
            
            
            $lstResults = $query->paginate(50)->appends(RequestQuery::except('page')); 

            return RestResource::collection($lstResults);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function publicImageGallery(Request $request){
        try{
             
            $lstFilter = $request->all();
            $lstFilter['status'] = 'published';
            $lstFilter['with_count'] = 'photos';
            $lstFilter['has__photos'] = 0;
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
     
}
