<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pbx;

class PbxController extends Controller
{
       
    public function __construct(Pbx $pbx)
    {
        $this->pbx = $pbx;        
    }

    
    public function index(Request $request)
    {
        $search = $request->input('search');
        $models = models();
        $connections = connections();
        
        if($search):
            $pbxes = $this->pbx->where('name','like', '%'.$search.'%')
                                ->orderBy('name')
                                ->paginate(30);
        
        else:    
            $pbxes = $this->pbx->orderBy('name')
                                ->paginate(30);
        endif;

        return view('configs.pbx.index', compact('pbxes','search','models','connections'));
    
    }


    public function create()
    {

        $models = models();
        $connections = connections();
        return view('configs.pbx.create', compact('models','connections'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|alpha_dash|unique:pbxes',
            'model' => 'required',       
            'connection' => 'required',       
        ]);
        
        try{
            $pbx = $this->pbx->create($request->all());
            toast(trans('messages.cad_suc_pbx'),'success');
            return redirect()->route('pbx.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_pbx'),'error');
            return redirect()->back();
        }

    }


    public function show($id)
    {
        $pbx = $this->pbx->findOrFail($id);
        $models = models();
        $connections = connections();
        
        return view('configs.pbx.show', compact('models','connections','pbx'));

    }


    public function edit($id)
    {
        $pbx = $this->pbx->findOrFail($id);
        $models = models();
        $connections = connections();
        
        return view('configs.pbx.edit', compact('models','connections','pbx'));

    }


    public function update(Request $request, $id)
    {
        $name = $this->pbx->where('name',$request->input('name'))->first();
        $request->validate([
            'name' => 'required|alpha_dash',
            'model' => 'required',       
            'connection' => 'required',       
        ]);

        if(isset($name->id)):
            if($name->id != $id):
                toast(trans('messages.edi_suc_pbx'),'error');
                return redirect()->back();

            endif;
        endif;
        
        try{
            $pbx = $this->pbx->findOrFail($id);
            $pbx->update($request->all());
            toast(trans('messages.edi_suc_pbx'),'success');
            return redirect()->route('pbx.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_pbx'),'error');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $models = models();
        $connections = connections();
        $pbx = $this->pbx->findOrFail($id);
        
        if(isset($del)):    
            return view('configs.pbx.delete',compact('models','connections','pbx'));            
        
        else:
            try{
                $pbx = $this->pbx->findOrFail($id);
                $pbx->delete();
                toast(trans('messages.del_suc_pbx'),'success');
                return redirect()->route('pbx.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_err_pbx'),'error');
                return redirect()->back();
            }        
        endif;
    }
}
