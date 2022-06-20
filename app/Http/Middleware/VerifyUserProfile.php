<?php

namespace App\Http\Middleware;

use App\Model\APIRoutePermission;
use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyUserProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user() == null){
            return response("Insufficent Permission!", 401);
        }

        //if user has role, we need to check if user can pass this middleware or not
        //if user didn't have role, it equals admin
        if(isset($request->user()['profile_ids']) &&  !empty($request->user()['profile_ids'])){ 

            $usrProfile = $request->user()['profile_ids'];
            $lstUserProfileIds = explode(',', $usrProfile);
            $reqMethod = $request->method();
            $reqUri = $request->getRequestUri(); 
            $path = $request->path(); 
             
            $isUpsert = strpos($path, 'upsert') !== false;

            //get route 
            $queryAPIPermission = APIRoutePermission::whereIn("profiles_id", $lstUserProfileIds);

            if($reqMethod == 'GET'){
                $queryAPIPermission->where("is_get", 1);
            }

            if($reqMethod == 'POST' || $isUpsert == true){
                $queryAPIPermission->where("is_post", 1);
            }

            if($reqMethod == 'DELETE'){
                $queryAPIPermission->where("is_delete", 1);
            }

            if($reqMethod == 'PUT' || $isUpsert == true){
                $queryAPIPermission->where("is_put", 1);
            }

            $queryAPIPermission->whereHas("apiRoute", function($query) use ($path){
                //remove for upsert url
                $path = str_replace("upsert-", "", $path);
                $query->where("url", '/'. $path);
            });
             

            $numRoute = $queryAPIPermission->count();//it will has 1 route if it has permission 

            return $numRoute > 0 ?  $next($request): response("Insufficient Permission!", 403); 
        }

        //if user doesnt have specific profile, it means they are admin
        return  $next($request); 
    }
}
