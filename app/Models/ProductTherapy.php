<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTherapy extends Model
{
    use SoftDeletes;
    
    protected $table = 'product_therapy';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}
