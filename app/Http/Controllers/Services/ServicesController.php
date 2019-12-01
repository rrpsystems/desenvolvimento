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
    public function __construct(Pbx $pbx, Call $call)
    {
        $this->pbx = $pbx;
        $this->call = $call;
        
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

    public function import()
    {    
        $imports = $this->pbx->get();
        
        foreach($imports as $import):
        
            $allfiles = Storage::disk('local')->files('bilhetes/'.$import->name);
            foreach ($allfiles as $file):
                trim(strtolower($import->model))($file,$import->name);
                mv_file($import->name,$file); 
            endforeach;
        endforeach;

    }

}
