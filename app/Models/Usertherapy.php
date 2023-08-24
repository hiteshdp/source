<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Therapy;

class Usertherapy extends Model
{
    use HasFactory;
    protected $table = 'user_therapy';

    public function therapy(){   
        return $this->belongsTo('App\Models\Therapy','therapyID');
    }
    
}

