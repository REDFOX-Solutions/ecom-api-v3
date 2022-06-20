<?php

namespace App\Model;



class UserRoles extends MainModel
{
    protected $table = 'user_roles';
   
    protected $primaryKey = 'id'; //table primary key column name, change it if it has different name
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = [];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id",
        "created_by_id",
        "created_date",
        "updated_by_id",
        "updated_date",
        "is_backup",
        "name",
        "description",
        "parent_role_id"

    ];//allow which fill to add into DB

    protected $casts = [ 
        "is_backup" => "integer"
    ];

    //formula fields
    protected $appends = [
        "created_by",
        "updated_by"
    ];//add custom formula field
    
    public function getCreatedByAttribute(){
        $createdBy = "Anonymous";
        if(isset($this->created_by_id)){
            $lstUsers = User::where("id", $this->created_by_id)
            ->get()
            ->toArray();
            $user = $lstUsers[0];

            // $createdBy = $user["lastname"] . " " . $this->created_date;
        }
        
        return $createdBy;
    }

    public function getUpdatedByAttribute(){
        $updatedBy = "Anonymous";
        if(isset($this->updated_by_id)){
            $lstUsers = User::where("id", $this->updated_by_id)
            ->get()
            ->toArray();
            $user = $lstUsers[0];

            // $updatedBy = $user["lastname"] . " " . $this->updated_date;
        }
        
        return $updatedBy;
    }
 
}