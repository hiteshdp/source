<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Product extends Model
{
    use SoftDeletes;
    
    protected $table = 'product';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;


    public static function getByBarcode($productId){
       return DB::table('product')->select("id",DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")") AS productName'))->where('productId',$productId)->get()->first();
    }
}
