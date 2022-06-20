<?php

namespace App\Model;



class GLAccMapping extends MainModel
{
    protected $table = 'gl_acc_mapping';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date'; 

    protected $fillable = [
        "id", 
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id",
        "is_backup",
        "object_name", 
        "obj_record_id",
        "acc_type",//inventory, inventory_sub, sale, sale_sub, cogs, cogs_sub, std_cost_var, std_cost_var_sub, std_cost_rev, std_cost_rev_sub, po_accr, po_accr_sub, deferral, deferral_sub
        "note",
        "code",
        "is_active",
        "chart_of_acc_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer"
    ];

    protected $appends = []; 
    
}
