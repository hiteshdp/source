<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    
    
    protected $table    = 'permissions';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;


    // accessor to return '-' when description is blank
    public function getDescriptionAttribute($value)
    {	
    	if($value == '' || $value === null){
    		return '-';
    	}
    	else{
    		return $value;
    	}
    }

    public function module(){
        return $this->hasOne('App\Models\Module','id','module');
    }

}
