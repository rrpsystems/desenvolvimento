<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    //
    protected $fillable = [
        'pbx','logdate','extensions_id','agentname','status_id',
        
    ];
}
