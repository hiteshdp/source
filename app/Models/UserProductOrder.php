<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProductOrder extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_product_order';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}
