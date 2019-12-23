<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected  $fillable = [
        'rname', 'routes_route', 'prefixes_service', 'type', 'direction', 'rate', 'connection', 'stime', 'ttmin', 'increment',
    ];
}
