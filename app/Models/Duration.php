<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    use SoftDeletes;
    
    protected $table = 'duration';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}
