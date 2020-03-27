<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'group',    
    ];

    public function gpExten(){

        return $this->hasMany('App\Models\Extension', 'groups_id', 'group');
    }
}
