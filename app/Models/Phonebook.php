<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phonebook extends Model
{
    protected $fillable = [
        'phonenumber', 'phonename',
        
    ];
}
