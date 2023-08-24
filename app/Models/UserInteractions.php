<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInteractions extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_interactions';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}
