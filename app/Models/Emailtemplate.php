<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Base_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Emailtemplate extends Base_Model {


    use SoftDeletes;
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $table    = 'emailtemplate';

    public $timestamps = true;
    
    protected $fillable = [
            'template_title',
            'subject',
            'content',
            'status',
            'created_at',
            'updated_at',
    ];
    
}