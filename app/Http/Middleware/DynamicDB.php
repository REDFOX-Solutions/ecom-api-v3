<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use App\Model\Clients;
use Closure;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * To make dynamic database
 * @createdDate 12-12-2020
 * @author Sopha Pum
 */
class DynamicDB
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
 
        $hasClient = $request->hasHeader('client');

        $url_array = explode('.', parse_url($request->url(), PHP_URL_HOST));
        $subdomain = $url_array[0];

        //if we cannot get client code from header, we will use client subdomain as namespace
        $clientName = ($hasClient ? $request->header('client') : $subdomain);
        $fieldName = ($hasClient ? 'code': 'namespace');
 
        $lstClients = [];

        $notSubdomain = ['127', 'www', 'localhost'];  

        // throw new CustomException("Route Client: $clientName , Field: $fieldName", 400); 

        try{
            if($hasClient && !in_array($clientName, $notSubdomain)){

                try{
                    $lstClients = Clients::where($fieldName, $clientName)
                                        ->where("is_active", 1)
                                        ->get()
                                        ->toArray();
    
                    //if there are no client match, return default database from env file
                    if(empty($lstClients)){
                        // return $next($request);
                        throw new CustomException("We have been unable to find your account! Please contact our support or try again later.", 400); 
                    }
                }catch(Exception $ex){
                    return $next($request);
                }
                
            }else{  
                return $next($request);
            } 
    
            $dbName = $lstClients[0]["db_name"];
    
            //here we setup dynamic database name
            Config::set('database.connections.mysql.database', $dbName);
            DB::purge('mysql');

        }catch(\Exception $ex){
            throw new CustomException("We have been unable to find your account! Please contact our support or try again later.", 400); 
        }
        
        return $next($request);
    }
}
