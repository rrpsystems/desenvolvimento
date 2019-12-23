<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefix extends Model
{
    protected $fillable = [
        'prefix','dadd','carrier','countrycode','areacode','country','locale','service',
        
    ];
}
