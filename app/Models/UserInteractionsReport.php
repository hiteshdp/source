<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInteractionsReport extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_interactions_report';
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    public $timestamps = true;
}