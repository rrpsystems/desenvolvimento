<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Connection;
use App\Models\Calls;

class ServicesController extends Controller
{
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        
    }

    //Coleta os bilhetes do PBX
    public function collector()
    {
        $conns = $this->connection->whereNotIn('type', ['Arquivo'])->whereNotNull('host')->get();
        
        foreach($conns as $conn):
            trim(strtolower($conn->type))($conn->name, $conn->host, $conn->port, $conn->user, $conn->password); 
            //dd($conn);
        endforeach;
    
    }

}
