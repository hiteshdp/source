<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrugsInteractions extends Model
{
    use SoftDeletes;
    
    protected $table = 'drugs_interactions';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}
