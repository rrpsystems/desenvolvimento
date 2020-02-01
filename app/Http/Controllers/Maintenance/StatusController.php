<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Call;
use App\Models\Pbx;
use App\Models\Trunk;
use App\Models\Extension;


class StatusController extends Controller
{
   
    public function __construct(Call $call, Trunk $trunk, Pbx $pbx, Extension $extension)
    {
        $this->call = $call;        
        $this->pbx = $pbx;        
        $this->trunk = $trunk;        
        $this->extension = $extension;        
    }


    public function index()
    {
        
        $extensions = collect([
            'NCadastrados' => $this->call
                            ->distinct('extensions_id')
                            ->leftJoin('extensions', 'extension', '=', 'extensions_id')
                            ->whereNull('extension')
                            ->where('accountcodes_id','')
                            ->orWhereNull('accountcodes_id')
                            ->count(),
            'Cadastrados' => $this->extension->count(),
        ]);
        
        $trunks = collect([
            'NCadastrados' => $this->call
                            ->distinct('trunks_id')
                            ->leftJoin('trunks', 'trunk', '=', 'trunks_id')
                            ->whereNull('trunk')    
                            ->whereIn('direction',['IC','OC'])    
                            ->count(),
            'Cadastrados' => $this->trunk->count(),
        ]);
        
        $calls = collect([
            'primeiro' => $this->call->orderBy('calldate', 'ASC')->first()->calldate,
            'ultimo'   => $this->call->orderBy('calldate', 'DESC')->first()->calldate,
            'total'    => $this->call->count(),
            'tarifadas' => $this->call->where('status_id', '1')->count(),
            'erros'    => $this->call->whereBetween('status_id',[91,99])->count(),
        ]);
        
        $extensions = json_decode(json_encode($extensions));
        $trunks = json_decode(json_encode($trunks));
        $calls = json_decode(json_encode($calls));
        return view('maintenances.status.index', compact('calls','extensions','trunks'));
    }

    public function show($id)
    {

        switch ($id):
            case 'trunks':
                $trunks = $this->call
                            ->distinct('trunks_id')
                            ->leftJoin('trunks', 'trunk', '=', 'trunks_id')
                            ->whereNull('trunk')    
                            ->whereIn('direction',['IC','OC']) 
                            ->get();
               
                return view('maintenances.status.trunks', compact('trunks'));
                break;
            
            case 'calls':
                $calls = $this->call->whereBetween('status_id',[91,99])->get();
               
                return view('maintenances.status.calls', compact('calls'));
                break;

            case 'extensions':
                $extensions = $this->call
                                ->distinct('extensions_id')
                                ->leftJoin('extensions', 'extension', '=', 'extensions_id')
                                ->whereNull('extension')
                                ->where('accountcodes_id','')
                                ->orWhereNull('accountcodes_id')
                                ->get();
               
                return view('maintenances.status.extensions', compact('extensions'));
                break;
        
            case 'accountscode':
                $accountscode = $this->call
                            ->distinct('trunks_id')
                            ->leftJoin('trunks', 'trunk', '=', 'trunks_id')
                            ->whereNull('trunk')    
                            ->get();
               
            return view('maintenances.status.trunks', compact('trunks'));
            break;
        endswitch;
        return redirect()->back();
    }

}
