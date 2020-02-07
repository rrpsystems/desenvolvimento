<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Extension;

class GroupsController extends Controller
{
 
    public function __construct(Group $group, Extension $extension)
    {
        $this->group = $group;        
        $this->extension = $extension;        
    }

    
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if($search):
            $groups = $this->group->where('group','like', '%'.$search.'%')
                                ->orderBy('group')
                                ->paginate(30);
        
        else:    
            $groups = $this->group->orderBy('group') ->paginate(30);

        endif;

        return view('configs.groups.index', compact('groups','search'));
    
    }


    public function create()
    {
        return view('configs.groups.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'group' => 'required|unique:groups',
        ]);
        
        try{
            $group = $this->group->create($request->all());
            toast(trans('messages.cad_suc_group'),'success');
            return redirect()->route('groups.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_group'),'error');
            return redirect()->back();
        }

    }


    public function show($id)
    {
        $group = $this->group->findOrFail($id);
        
        return view('configs.groups.show', compact('group'));

    }


    public function edit($id)
    {
        $group = $this->group->findOrFail($id);
        
        return view('configs.groups.edit', compact('group'));

    }


    public function update(Request $request, $id)
    {
        $group = $this->group->where('group',$request->input('group'))->first();
        $request->validate([
            'group' => 'required',
        ]);

        if(isset($group->id)):
            if($group->id != $id):
                toast(trans('messages.edi_err_group'),'error');
                return redirect()->back();

            endif;
        endif;
        
        try{
            $group = $this->group->findOrFail($id);
            $group->update($request->all());
            toast(trans('messages.edi_suc_group'),'success');
            return redirect()->route('groups.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_group'),'error');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $group = $this->group->findOrFail($id);
        
        if(isset($del)):    
            return view('configs.groups.delete',compact('group'));            
        
        else:
            try{
                $group = $this->group->findOrFail($id);
                $group->delete();
                toast(trans('messages.del_suc_group'),'success');
                return redirect()->route('groups.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_err_group'),'error');
                return redirect()->back();
            }        
        endif;
    }
}