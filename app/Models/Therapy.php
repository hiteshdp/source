<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usertherapy;

class Therapy extends Model
{
    use HasFactory;
    protected $table = 'therapy';

    public function usertherapy(){   
        return $this->hasOne('App\Models\Usertherapy');
    }
}
