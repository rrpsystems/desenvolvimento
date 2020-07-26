<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departament;
use App\Models\Extension;
use App\Models\Group;
use App\Models\Pbx;
use App\User;

class ExtensionsController extends Controller
{
    public function __construct(Extension $extension, Pbx $pbx, User $user, Group $group, Departament $departament)
    {
        $this->departament = $departament;        
        $this->extension = $extension;        
        $this->group = $group;        
        $this->user = $user;        
        $this->pbx = $pbx;        
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if($search):
            $extensions = $this->extension->where('extension','like', '%'.$search.'%')
                                ->orderBy('extension')
                                ->paginate(30);
        
        else:    
            $extensions = $this->extension->orderBy('extension')
                                ->paginate(30);
        endif;

        return view('configs.extensions.index', compact('extensions','search'));

    }

    public function create(Request $request)
    {
        $extension = collect([
            'page' => 'start', 
            ]);

        if($request->input('extension')):
            $extension = collect([
                'pbxes_id' => $request->input('pbxes_id'),
                'extension' => $request->input('extension'), 
                'ename' => $request->input('ename'), 
                'page' => 'error', 
            ]);

        endif;
        $extension = json_decode(json_encode($extension));
        
        $pbxes  = $this->pbx->get();
        $groups  = $this->group->get();
        $departaments  = $this->departament->get();
        $users  = $this->user->get();

        return view('configs.extensions.create', compact('extension','pbxes','users','groups','departaments'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'extension' => 'required|unique:extensions,extension,NULL,id,pbxes_id,' . $request->pbxes_id, 
            'pbxes_id'  => 'required|unique:extensions,pbxes_id,NULL,id,extension,' . $request->extension,       
        ]);
        
        try{
            $extension = $this->extension->create($request->all());
            toast(trans('messages.cad_suc_extension'),'success');
             
            if($request->input('page')=='error'):
                return redirect()->route('status.show',['extensions']);
            
            else:
                return redirect()->route('extensions.index');
            endif;

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_extension'),'error');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $extension = $this->extension->findOrFail($id);
        $departaments  = $this->departament->get();
        $groups  = $this->group->get();
        $users  = $this->user->get();
        $pbxes  = $this->pbx->get();

        return view('configs.extensions.show', compact('extension','pbxes','users','groups','departaments'));

    }

    public function edit($id)
    {
        $extension = $this->extension->findOrFail($id);
        $departaments  = $this->departament->get();
        $groups  = $this->group->get();
        $users  = $this->user->get();
        $pbxes  = $this->pbx->get();
        
        return view('configs.extensions.edit', compact('extension','pbxes','users','groups','departaments'));
    }

    public function update(Request $request, $id)
    {
        $extension = $this->extension->where('extension',$request->input('extension'))->where('pbxes_id',$request->input('pbxes_id'))->first();
        
        $request->validate([
            'extension' => 'required', 
            'pbxes_id' => 'required',           
        ]);

        if(isset($extension->id)):
            if($extension->id != $id):
                toast(trans('messages.edi_err_extension'),'error');
                $request->validate([
                    'extension' => 'required|unique:extensions,extension,NULL,id,pbxes_id,' . $request->pbxes_id, 
                    'pbxes_id'  => 'required|unique:extensions,pbxes_id,NULL,id,extension,' . $request->extension,  
                ]);
                
                return redirect()->back();

            endif;
        endif;
        
        try{
            $extension = $this->extension->findOrFail($id);
            $extension->update($request->all());
            toast(trans('messages.edi_suc_extension'),'success');
            return redirect()->route('extensions.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_extension'),'error');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $extension = $this->extension->findOrFail($id);
        $departaments  = $this->departament->get();
        $groups  = $this->group->get();
        $users  = $this->user->get();
        $pbxes  = $this->pbx->get();
        
        if(isset($del)):    
            return view('configs.extensions.delete', compact('extension','pbxes','users','groups','departaments'));
            
        else:
            try{
                
                $extentension = $this->extension->findOrFail($id);
                $extentension->delete();
                toast(trans('messages.del_suc_extension'),'success');
                return redirect()->route('extensions.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.cad_err_extension'),'error');
                return redirect()->back();
            }        
        endif;
    }
}
