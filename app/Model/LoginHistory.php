<?php

namespace App\Model;


use Illuminate\Support\Carbon;

class LoginHistory extends MainModel
{
    protected $table = 'login_histories';
    protected $keyType = 'string';
    public $incrementing = false; 
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id",
		"login_time",
		"platform",
		"login_url",
		"location",
		"source_ip",
		"users_id",
		"is_backup",
		"created_date",
		"updated_date",
		"reason",
		"username",
		"is_success"
	];
	
	protected $casts = [
		"id" => "string",
		'is_success' => 'integer',
		"is_backup" => "integer"
    ];

    //to set format field
    public function setLoginTimeAttribute($value){
        $this->attributes['login_time'] = Carbon::now()->toDateTimeString();
    }
}
