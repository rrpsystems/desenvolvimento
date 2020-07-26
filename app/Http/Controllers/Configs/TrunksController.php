<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trunk;
use App\Models\Pbx;
use App\Models\Route;

class TrunksController extends Controller
{
    public function __construct(Trunk $trunk, Pbx $pbx, Route $route)
    {
        $this->trunk = $trunk;        
        $this->pbx = $pbx;        
        $this->route = $route;        
    }

    public function index(Request $request)
    {
        
        $search = $request->input('search');
        
        if($search):
            $trunks = $this->trunk->where('trunk','like', '%'.$search.'%')
                                ->orderBy('trunk')
                                ->paginate(30);
        
        else:    
            $trunks = $this->trunk->orderBy('trunk')
                                ->paginate(30);
        endif;

        return view('configs.trunks.index', compact('trunks','search'));
    
    }
 

    public function create(Request $request)
    {
            $trunk = collect([
                'page' => 'start', 
                ]);

            if($request->input('trunk')):
                $trunk = collect([
                    'tpbx' => $request->input('tpbx'),
                    'trunk' => $request->input('trunk'), 
                    'tname' => $request->input('tname'), 
                    'page' => 'error', 
                ]);

            endif;
            $trunk = json_decode(json_encode($trunk));
            $pbxes  = $this->pbx->get();
            $routes = $this->route->get();
            
            return view('configs.trunks.create', compact('trunk','pbxes','routes'));

    }


    public function store(Request $request)
    {
        $request->validate([
            'trunk' => 'required|unique:trunks,trunk,NULL,id,tpbx,' . $request->tpbx, 
            'tpbx' => 'required',       
            'routes_route' => 'required',       
        ]);
        
        try{
            $trunk = $this->trunk->create($request->all());
            toast(trans('messages.cad_suc_trunk'),'success');
            
            if($request->input('page')=='error'):
                return redirect()->route('status.show',['trunks']);
            else:
                return redirect()->route('trunks.index');
            endif;

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_trunk'),'error');
            return redirect()->back();
        }
    }


    public function show($id)
    {
        $trunk  = $this->trunk->findOrFail($id);
        $pbxes  = $this->pbx->get();
        $routes = $this->route->get();

        return view('configs.trunks.show', compact('trunk','pbxes','routes'));

    }


    public function edit($id)
    {
        $trunk  = $this->trunk->findOrFail($id);
        $pbxes  = $this->pbx->get();
        $routes = $this->route->get();
        return view('configs.trunks.edit', compact('trunk','pbxes','routes'));
    }


    public function update(Request $request, $id)
    {
        
        $trunk = $this->trunk->where('trunk',$request->input('trunk'))->where('tpbx',$request->input('tpbx'))->first();
        
        $request->validate([
            'trunk' => 'required', 
            'tpbx' => 'required',       
            'routes_route' => 'required',      
        ]);

        if(isset($trunk->id)):
            if($trunk->id != $id):
                toast(trans('messages.edi_err_trunk'),'error');
                $request->validate([
                    'trunk' => 'unique:trunks,trunk,NULL,id,tpbx,' . $request->tpbx, 
                ]);
                
                return redirect()->back();

            endif;
        endif;
        
        try{
            $trunk = $this->trunk->findOrFail($id);
            $trunk->update($request->all());
            toast(trans('messages.edi_suc_trunk'),'success');
            return redirect()->route('trunks.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_trunk'),'error');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $trunk  = $this->trunk->findOrFail($id);
        $pbxes  = $this->pbx->get();
        $routes = $this->route->get();

        if(isset($del)):    
            return view('configs.trunks.delete', compact('trunk','pbxes','routes'));

        else:
            try{
                $trunk  = $this->trunk->findOrFail($id);
                $trunk->delete();
                toast(trans('messages.del_suc_trunk'),'success');
                return redirect()->route('trunks.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_err_trunk'),'error');
                return redirect()->back();
            }        
        endif;
    }
    
}
