<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Tenant;

class SectionsController extends Controller
{
    public function __construct(Section $section, Tenant $tenant)
    {
        $this->section = $section;        
        $this->tenant = $tenant;        
    }

    public function index(Request $request)
    {
        
        $search = $request->input('search');
        
        if($search):
            $sections = $this->section->where('section','like', '%'.$search.'%')
                                ->orderBy('section')
                                ->paginate(30);
        
        else:    
            $sections = $this->section->orderBy('section')
                                ->paginate(30);
        endif;

        return view('configs.sections.index', compact('sections','search'));
    
    }
 

    public function create()
    {
        $tenants    = $this->tenant->get();
        return view('configs.sections.create', compact('tenants'));

    }


    public function store(Request $request)
    {

        $request->validate([
            'section' => 'required|unique:sections,section,NULL,id,tenants_id,' . $request->tenants_id, 
            'tenants_id' => 'required',       
        ]);
        
        try{
            $section = $this->section->create($request->all());
            toast(trans('messages.cad_suc_section'),'success');
            return redirect()->route('sections.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_section'),'error');
            return redirect()->back();
        }
    }


    public function show($id)
    {
        $section  = $this->section->findOrFail($id);
        $tenants = $this->tenant->get();

        return view('configs.sections.show', compact('section','tenants'));

    }


    public function edit($id)
    {
        $section  = $this->section->findOrFail($id);
        $tenants = $this->tenant->get();

        return view('configs.sections.edit', compact('section','tenants'));
    }


    public function update(Request $request, $id)
    {
        
        $section = $this->section->where('section',$request->input('section'))->where('tenants_id',$request->input('tenants_id'))->first();
        
        $request->validate([
            'section' => 'required', 
            'tenants_id' => 'required',      
        ]);

        if(isset($section->id)):
            if($section->id != $id):
                toast(trans('messages.edi_err_section'),'error');
                $request->validate([
                    'section' => 'unique:sections,section,NULL,id,tenants_id,' . $request->tenants_id, 
                ]);
                
                return redirect()->back();

            endif;
        endif;
        
        try{
            $section = $this->section->findOrFail($id);
            $section->update($request->all());
            toast(trans('messages.edi_suc_section'),'success');
            return redirect()->route('sections.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_section'),'error');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $section  = $this->section->findOrFail($id);
        $tenants = $this->tenant->get();

        if(isset($del)):    
            return view('configs.sections.delete', compact('section','tenants'));

        else:
            try{
                $section  = $this->section->findOrFail($id);
                $section->delete();
                toast(trans('messages.del_suc_section'),'success');
                return redirect()->route('sections.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_err_section'),'error');
                return redirect()->back();
            }        
        endif;
    }

}
