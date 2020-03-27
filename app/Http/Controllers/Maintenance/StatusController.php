<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Call;
use App\Models\Pbx;
use App\Models\Trunk;
use App\Models\Extension;
use App\Models\Accountcode;
use App\Models\Prefix;
use App\User;


class StatusController extends Controller
{
   
    public function __construct(Call $call, Trunk $trunk, Pbx $pbx, Extension $extension, Accountcode $accountcode, User $user, Prefix $prefix)
    {
        $this->call = $call;        
        $this->pbx = $pbx;        
        $this->trunk = $trunk;        
        $this->extension = $extension;        
        $this->accountcode = $accountcode;        
        $this->user = $user;        
        $this->prefix = $prefix;        
    }


    public function index()
    {
        //toast(trans('messages.users'),'success');
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
        
        $accountcodes = collect([
            'NCadastrados' => $this->call
                            ->distinct('accountcodes_id')
                            ->leftJoin('accountcodes', 'accountcode', '=', 'accountcodes_id')
                            ->whereNull('accountcode')
                            ->where('accountcodes_id','<>','')
                            ->count(),
            'Cadastrados' => $this->accountcode->count(),
        ]);
        
        $trunks = collect([
            'NCadastrados' => $this->call
                            ->distinct('trunks_id')
                            ->where('status_id','92')   
                            ->count(),
            'Cadastrados' => $this->trunk->count(),
        ]);
        
        $calls = collect([
            'primeiro'  => $this->call->orderBy('calldate', 'ASC')->first()->calldate,
            'ultimo'    => $this->call->orderBy('calldate', 'DESC')->first()->calldate,
            'total'     => $this->call->count(),
            'tarifadas' => $this->call->where('status_id', '1')->count(),
            'erros'     => $this->call->whereBetween('status_id',[91,99])->count(),
        ]);
        
        $informations = collect([
            'users'     => $this->user->count(),
            'prefixes'  => $this->prefix->count(),
        ]);

        $extensions   = json_decode(json_encode($extensions));
        $accountcodes = json_decode(json_encode($accountcodes));
        $trunks       = json_decode(json_encode($trunks));
        $calls        = json_decode(json_encode($calls));
        $informations = json_decode(json_encode($informations));
   
        return view('maintenances.status.index', compact('accountcodes','calls','extensions','trunks','informations'));
    }

    public function show($id)
    {

        switch ($id):
            case 'trunks':
                
                $trunks = $this->call->distinct('trunks_id')
                                        ->where('status_id','92')
                                        ->get();
               
                return view('maintenances.status.trunks', compact('trunks'));
                break;
            
            case 'calls':
                $calls = $this->call->whereBetween('status_id',[91,99])->paginate(50);
               
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
        
            case 'accountcodes':
                $accountcodes = $this->call
                                ->distinct('accountcodes_id')
                                ->leftJoin('accountcodes', 'accountcode', '=', 'accountcodes_id')
                                ->whereNull('accountcode')
                                ->where('accountcodes_id','<>','')
                                ->get();
               
            return view('maintenances.status.accountcodes', compact('accountcodes'));
            break;
        endswitch;
        return redirect()->back();
    }

}
