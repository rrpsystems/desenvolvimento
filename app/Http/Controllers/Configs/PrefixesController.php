<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prefix;

class PrefixesController extends Controller
{
    

        public function __construct(Prefix $prefix)
    {
        $this->prefix = $prefix;        
    }


    
    public function index(Request $request)
    {
        $search = $request->input('search');
        $services = services();
        
        if($search):
            $prefixes = $this->prefix->where('prefix','like', $search.'%')
                                ->orderBy('areacode')
                                ->orderBy('service')
                                ->orderBy('prefix')
                                ->paginate(50);
        
        else:    
            $prefixes = $this->prefix->orderBy('areacode')
                                ->orderBy('service')
                                ->orderBy('prefix')
                                ->paginate(50);
        endif;

        return view('configs.prefixes.index', compact('prefixes','search','services'));

    }

    
    public function create()
    {
        $services = services();
        return view('configs.prefixes.create', compact('services'));

    }

    
    public function store(Request $request)
    {
        $request->validate([
            'prefix' => 'required|unique:prefixes',
            'service' => 'required',       
            'locale' => 'required',       
        ]);
        
        try{
            $prefix = $this->prefix->create($request->all());
            toast(trans('messages.cad_suc_prefix'),'success');
            return redirect()->route('prefixes.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_prefix'),'error');
            return redirect()->back();
        }

    }

    public function show($id)
    {
        $prefix = $this->prefix->findOrFail($id);
        $services = services();
        
        return view('configs.prefixes.show', compact('services','prefix'));

    }

    
    public function edit($id)
    {
        $prefix = $this->prefix->findOrFail($id);
        $services = services();
        
        return view('configs.prefixes.edit', compact('services','prefix'));

    }

    public function update(Request $request, $id)
    {
        $prefix = $this->prefix->where('prefix',$request->input('prefix'))->first();
        $request->validate([
            'prefix' => 'required',
            'service' => 'required',       
            'locale' => 'required',       
        ]);

        if(isset($prefix->id)):
            if($prefix->id != $id):
                toast(trans('messages.edi_err_prefix'),'error');
                return redirect()->back();

            endif;
        endif;
        
        try{
            $prefix = $this->prefix->findOrFail($id);
            $prefix->update($request->all());
            toast(trans('messages.edi_suc_prefix'),'success');
            return redirect()->route('prefixes.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_prefix'),'error');
            return redirect()->back();
        }
    }

    
    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $services = services();
        $prefix = $this->prefix->findOrFail($id);
        
        if(isset($del)):    
            return view('configs.prefixes.delete',compact('services','prefix'));            
        
        else:
            try{
                $prefix = $this->prefix->findOrFail($id);
                $prefix->delete();
                toast(trans('messages.del_suc_prefix'),'success');
                return redirect()->route('prefixes.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_err_prefix'),'error');
                return redirect()->back();
            }        
        endif;
    }
}
