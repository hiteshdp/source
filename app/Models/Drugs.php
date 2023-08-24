<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Drugs extends Model
{
    use SoftDeletes;
    
    protected $table = 'drugs';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}
