<?php

namespace App\Model;
 
use Illuminate\Foundation\Auth\User as Authenticatable; 

class User extends Authenticatable
{
    protected $table = 'users';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs', 'profiles'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id",
		"title",
		"phone",
		"email",
		"username",
		"district",
		"commune",
		"country",
		"city",
		"password",
		"api_token",
		"photo",
		"is_active",
		"is_locked",
		"created_date",
		"created_by_id",
		"updated_date",
		"updated_by_id",
		"currency_code",
		"user_type",
		"is_admin",
		"status",
		"user_roles_ids",
		"is_backup",
		"user_code",
		"company_id",
		"lock_reason",
		"user_permission_id",
		"record_type_id",
		"profile_ids",
		"alias",
		"score",
		"timezone"
    ];

	protected $casts = [ 
        "is_backup" => "integer",
		"is_active" => "integer",
		"is_locked" => "integer",
		"score" => "double"
    ];
	
	protected $appends = ['photo_preview', "record_type_name", "user_roles", "applications"];

	public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
	}

	public function getRecordTypeNameAttribute(){
        return "{$this->photo}";
	}

	/**
	 * Method get available Application for current user
	 */
	public function getApplicationsAttribute(){  

		if(!isset($this->profile_ids) || empty($this->profile_ids)){
			$lstApps = Applications::where("active", 1)
									->orderBy("ordering", 'asc')
									->with(["menus", "modules"])
									->get()
									->toArray();
			return $lstApps;
		}

		if(isset($this->profile_ids)){
			$profileIds = $this->profile_ids;  
			$lstProfileIds = (strpos($profileIds, ',') !== false) ? explode(",", $profileIds) : [$profileIds];
			 
			//get related profile_permission
			$mapObjPerms = ProfilePermission::whereIn("profiles_id", $lstProfileIds)
												->where("is_visible", 1)
												->get()
												->mapToGroups(function($item, $key){
													return [$item["object_name"] => $item["record_id"]];
												})
												->toArray(); 
			
			//if there are no application asssign to this profile, we dont need to do anything
			if(!isset($mapObjPerms["applications"]) && empty($mapObjPerms["applications"])) return [];
 
			//get all application which is filter from Profile_Permission
			//get app menu from related application and filter from profile_permission
			//get app module from related application and filter from profile_permission
			$lstApps = Applications::whereIn("id", $mapObjPerms["applications"])
									->where("active", 1)
									->orderBy("ordering", 'asc')
									->with(["menus", "modules"])
									->get()
									->toArray();
 
			return $lstApps;
			$lstMenuPerms = isset($mapObjPerms["app_menus"]) ? $mapObjPerms["app_menus"]: [];

			$lstModulePerms = isset($mapObjPerms["app_modules"]) ? $mapObjPerms["app_modules"]: [];

			$collectionMenus = collect($lstMenuPerms);
			$collectionModules = collect($lstModulePerms);
 
			//filter menu and modules that didn't has in permission
			foreach ($lstApps as $index => &$app) {
				$lstMenus = isset($app["menus"]) ? $app["menus"] : [];
				$lstModules = isset($app["modules"]) ? $app["modules"] : []; 

				$lstNewMenus = [];
				$lstNewModules = [];

				foreach ($lstMenus as $index => $menu) {
					if($collectionMenus->contains($menu["id"])){
						$lstNewMenus[] = $menu;
					}
				}

				foreach ($lstModules as $index => $module) {
					if($collectionModules->contains($module["id"])){
						$lstNewModules[] = $module;
					}
				}

				$app["menus"] = $lstNewMenus;
				$app["modules"] = $lstNewModules;

			}
			
			return $lstApps;
			
		}

		return [];
		
	}
	
	protected $hidden = [
        'password'
	];
	
    public function company(){
        return $this->belongsTo('App\Model\Company', 'company_id')->with('metaDataConfigs');
	}
	 	
	public function userPermission(){
		return $this->belongsTo('App\Model\Permissions', 'user_roles_id');
	}
	
    public function langs(){
        return $this->hasMany('App\Model\UserTranslation', 'users_id', 'id');
    }

    public function loginHistories(){
        return $this->hasMany('App\Model\LoginHistory', 'username', 'username')->orderBy("login_time", "desc");
	}

	public function profiles(){
		return $this->belongsTo('App\Model\Profile', 'profile_ids');
	}
	
	//to set value or convert value for create/update
    public function setPasswordAttribute($value){
        $this->attributes['password'] = bcrypt($value);
	}
	
	public function getUserRolesAttribute(){
        // $categoryIds = $this->category_ids; 
        // $lstCategoryIds = explode(",", $categoryIds);
		// return Categories::whereIn("id", $lstCategoryIds)->get(); 
		return '';
    }
}
