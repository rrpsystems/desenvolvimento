<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    //
    protected $fillable = [
        'pbx','calldate','extensions_id','trunks_id','did','direction','dialnumber','callnumber','prefixes_id','ring','duration','billsec','accountcodes_id','projectcodes_id','disposition','uniqueid','rates_id','rate','status_id'
        
    ];
}
