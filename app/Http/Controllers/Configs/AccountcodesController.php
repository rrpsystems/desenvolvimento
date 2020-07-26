<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departament;
use App\Models\Accountcode;
use App\Models\Group;
use App\Models\Pbx;
use App\User;


class AccountcodesController extends Controller
{

    public function __construct(Accountcode $accountcode, Pbx $pbx, User $user, Group $group, Departament $departament)
    {
        $this->departament = $departament;        
        $this->accountcode = $accountcode;        
        $this->group = $group;        
        $this->user = $user;        
        $this->pbx = $pbx;        
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if($search):
            $accountcodes = $this->accountcode->where('accountcode','like', '%'.$search.'%')
                                ->orderBy('accountcode')
                                ->paginate(30);
        
        else:    
            $accountcodes = $this->accountcode->orderBy('accountcode')
                                ->paginate(30);
        endif;

        return view('configs.accountcodes.index', compact('accountcodes','search'));

    }

    public function create(Request $request)
    {
        $accountcode = collect([
            'page' => 'start', 
            ]);

        if($request->input('accountcode')):
            $accountcode = collect([
                'pbxes_id' => $request->input('pbxes_id'),
                'accountcode' => $request->input('accountcode'), 
                'aname' => $request->input('aname'), 
                'page' => 'error', 
            ]);

        endif;
        $accountcode = json_decode(json_encode($accountcode));
        
        $pbxes  = $this->pbx->get();
        $groups  = $this->group->get();
        $departaments  = $this->departament->get();
        $users  = $this->user->get();

        return view('configs.accountcodes.create', compact('accountcode','pbxes','users','groups','departaments'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'accountcode' => 'required|unique:accountcodes,accountcode,NULL,id,pbxes_id,' . $request->pbxes_id, 
            'pbxes_id'  => 'required|unique:accountcodes,pbxes_id,NULL,id,accountcode,' . $request->accountcode,       
        ]);
        
        try{
            $accountcode = $this->accountcode->create($request->all());
            toast(trans('messages.cad_suc_accountcode'),'success');
            
            if($request->input('page')=='error'):
                return redirect()->route('status.show',['accountcodes']);
            
            else:
                return redirect()->route('accountcodes.index');
            endif;

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_accountcode'),'error');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $accountcode = $this->accountcode->findOrFail($id);
        $departaments  = $this->departament->get();
        $groups  = $this->group->get();
        $users  = $this->user->get();
        $pbxes  = $this->pbx->get();

        return view('configs.accountcodes.show', compact('accountcode','pbxes','users','groups','departaments'));

    }

    public function edit($id)
    {
        $accountcode = $this->accountcode->findOrFail($id);
        $departaments  = $this->departament->get();
        $groups  = $this->group->get();
        $users  = $this->user->get();
        $pbxes  = $this->pbx->get();
        
        return view('configs.accountcodes.edit', compact('accountcode','pbxes','users','groups','departaments'));
    }

    public function update(Request $request, $id)
    {
        $accountcode = $this->accountcode->where('accountcode',$request->input('accountcode'))->where('pbxes_id',$request->input('pbxes_id'))->first();
        
        $request->validate([
            'accountcode' => 'required', 
            'pbxes_id' => 'required',           
        ]);

        if(isset($accountcode->id)):
            if($accountcode->id != $id):
                toast(trans('messages.edi_err_accountcode'),'error');
                $request->validate([
                    'accountcode' => 'required|unique:accountcodes,accountcode,NULL,id,pbxes_id,' . $request->pbxes_id, 
                    'pbxes_id'  => 'required|unique:accountcodes,pbxes_id,NULL,id,accountcode,' . $request->accountcode,  
                ]);
                
                return redirect()->back();

            endif;
        endif;
        
        try{
            $accountcode = $this->accountcode->findOrFail($id);
            $accountcode->update($request->all());
            toast(trans('messages.edi_suc_accountcode'),'success');
            return redirect()->route('accountcodes.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_accountcode'),'error');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $accountcode = $this->accountcode->findOrFail($id);
        $departaments  = $this->departament->get();
        $groups  = $this->group->get();
        $users  = $this->user->get();
        $pbxes  = $this->pbx->get();
        
        if(isset($del)):    
            return view('configs.accountcodes.delete', compact('accountcode','pbxes','users','groups','departaments'));
            
        else:
            try{
                
                $extentension = $this->accountcode->findOrFail($id);
                $extentension->delete();
                toast(trans('messages.del_suc_accountcode'),'success');    
                return redirect()->route('accountcodes.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_suc_accountcode'),'error');
                return redirect()->back();
            }        
        endif;
}
}
