<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accountcode extends Model
{
    protected $fillable = [
        'accountcode', 'pbxes_id', 'aname', 'groups_id', 'departaments_id', 'users_id',
        
    ];
}
