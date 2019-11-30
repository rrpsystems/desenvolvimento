<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Pbx;
use App\Models\Call;

class ServicesController extends Controller
{
    public function __construct(Pbx $pbx)
    {
        $this->pbx = $pbx;
        
    }

    //Coleta os bilhetes do PBX
    public function collector()
    {
        $conns = $this->pbx->whereNotIn('connection', ['Arquivo'])->whereNotNull('host')->get();
        
        foreach($conns as $conn):
            trim(strtolower($conn->connection))($conn->name, $conn->host, $conn->port, $conn->user, $conn->password); 
            //dd($conn);
        endforeach;
    
    }

}
