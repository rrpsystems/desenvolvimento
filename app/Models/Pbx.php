<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pbx extends Model
{
    protected $fillable = [
        'name','model','connection','host','port','user','password','interval',
        
    ];
}
