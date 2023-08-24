<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserIntegrativeProtocol extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_integrative_protocol';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}
