<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Call;


class RebillingController extends Controller
{
    public function __construct(Call $call)
    {
        $this->call = $call;            
    }

   public function index(Request $request)
    {
        //
      //  $calls = $this->call->where('status_id','<>', '1')->get();
       // dd($calls);
       return view('maintenances.rebillings.index');
    }

    
    public function store(Request $request)
    {
        if($request->input('billing') == 'errors'):
            $calls = $this->call->whereBetween('status_id',[91,99])->get();

            foreach($calls as $call):
                $callnumber=''; $update='';
                
                if($call->direction == 'IC'):
                    $callnumber = dialIc($call->dialnumber, $call->trunks_id, $call->pbx);
                
                elseif($call->direction == 'OC'):
                    $callnumber = dialOc($call->dialnumber, $call->trunks_id, $call->pbx);
                
                endif;
                $update = $this->call->where('id',$call->id)
                                                    ->update([
                                                    'callnumber' => $callnumber,
                                                    'status_id' => '0'
                    ]);
                //dd($call);
            
            endforeach;
            
        elseif($request->input('billing') == 'period'):
        
        else:
            dd($request->all());
        
        endif;
    }

}
