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

        return view('maintenances.rebillings.index');
    }

    
    public function store(Request $request)
    {
        if($request->input('billing') == 'errors'):
            $calls = $this->call->whereBetween('status_id',[91,99])->get();

        elseif($request->input('billing') == 'period'):
            $start_datetime = $request->input('start_date').' 00:00:00';
            $end_datetime   = $request->input('end_date').' 23:59:59';
            
            $request->validate([
            
                'start_date' => 'required|date|before:end_date',
                'end_date' => 'required|date|after:start_date',
            ]);
                
            $calls = $this->call->whereBetween('calldate', [$start_datetime, $end_datetime])->get();
            
        else:    
            $calls = $this->call->get();
            
        endif;
        
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
            
        endforeach;

        toast(trans('messages.rebilling_calls'), 'success');
        return redirect()->back();
    }

}
