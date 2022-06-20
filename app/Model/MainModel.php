<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MainModel extends Model
{

    protected $primaryKey = 'id'; //table primary key column name, change it if it has different name
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increas 
    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name


    public static $relationship = [];
    /**
     * E.g.
     * public static $relationship = [
     *   "children" => [ 
     *       [
     *           "name" => "options", 
     *           "parent_field" => "option_master_id",
     *           "controller" => "App\Http\Controllers\API\ProductsController"],
     *       [
     *           "name" => "properties", 
     *           "parent_field" => "products_id",
     *           "controller" => "App\Http\Controllers\API\ProductPropertyController"],
     *   ],
     *   "parent" => []
     *];
     */

    public function createdBy(){
        return $this->belongsTo('App\Model\User', 'created_by_id');
    }

    public function updatedBy(){
        return $this->belongsTo('App\Model\User', 'updated_by_id');
    }

    public function getCreatedAttribute(){
             $createdBy = "Anonymous";
             if(isset($this->created_by_id)){
            $lstUsers = User::where("id", $this->created_by_id)
                            ->get()
                            ->toArray();
            $user = $lstUsers[0];
            // $createdBy = $user["lastname"] . " at " . $this->created_date;
        }
        
        return $createdBy;
    }
        
    public function getUpdatedAttribute(){
        $updatedBy = "Anonymous";
        if(isset($this->updated_by_id)){
            $lstUsers = User::where("id", $this->updated_by_id)
                            ->get()
                            ->toArray();
            $user = $lstUsers[0];
            // $updatedBy = $user["lastname"] . " at " . $this->updated_date;
        }
        
        return $updatedBy;
    }
}
