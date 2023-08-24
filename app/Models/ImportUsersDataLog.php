<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportUsersDataLog extends Model
{
    use HasFactory;

    protected $table = 'import_users_data_log';

    public $timestamps = true;
}
