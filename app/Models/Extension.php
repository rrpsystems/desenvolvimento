<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = [
        'extension', 'pbxes_id', 'ename', 'groups_id', 'departaments_id', 'users_id',
        
    ];
}
