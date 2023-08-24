<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\Helpers\Helpers;

class ProductRecommendations extends Model
{
    use SoftDeletes;
    
    protected $table = 'products_recommendations';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;

    /**
     * Get the products data by the related therapy id given
     *  */    
    public static function getRecommendedProductData($therapyId){
        $data = ProductRecommendations::select('productImageLink',
        DB::raw('CONCAT(product.productName," by ",product.productBrand) AS productName'),
        'product_ratings as productRatings','product_review_count as productReviewCount','product_price as productPrice',
        'product_url as productUrl')
        ->where('therapy_id',$therapyId)
        ->leftJoin('product','products_recommendations.product_id','=','product.productId')
        ->orderBy('rank_order','ASC')
        ->get()->toArray();
        // Add the additional key values in the result from loop array
        foreach($data as $key => $value){
            // Store the star ratings value as width percentage value to display accordingly in the view page
            $data[$key]['productStarRating'] = $value['productRatings'] * 20;
            // Add the superscript tag if point value exists
            $data[$key]['productPriceSuperScript'] = "$".Helpers::priceSuperScript($value['productPrice']);
        }

        return $data;
    }
}
