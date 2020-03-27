<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use DB;
use Alert;

class RolesController extends Controller
{

    public function __construct(Role $role, Permission $permission )
    {
        $this->role = $role;
        $this->permission = $permission;
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if($search):
            $roles = $this->role->orderBy('name')
                                ->where('name','like', '%'.$search.'%')
                                ->whereNotIn('name', ['Root'])
                                ->paginate(30);
        else:    
            $roles = $this->role->orderBy('name')
                                ->whereNotIn('name', ['Root'])
                                ->paginate(30);
        endif;
        return view('configs.roles.index',compact('roles','search'));
    
    }


    public function create()
    {
        $permissions = $this->permission->distinct()->get()
                                            ->groupBy( function($item) {
                                                list($role, $action) = explode("-", $item->name);
                                                return $role;
                                            //return $item->created_at->format('Y-m-d');
                                        });
            $selected = isset($selected) ? $selected : 'empty';
            $selected = collect($selected);        
            return view('configs.roles.create',compact('permissions','selected'));

    }


    public function store(Request $request)
    {
        try{
            $role = $this->role->create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));
            toast(trans('messages.cad_suc_rule'),'success');
            return redirect()->route('roles.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_rule'),'error');
            return redirect()->back();

        }
    }


    public function show($id)
    {
        $roles = $this->role->findOrFail($id);
        $permissions = $this->permission->distinct()->orderBy('name')->get()
                                            ->groupBy( function($item) {
                                                
                                                list($role, $action) = explode("-", $item->name);
                                                return $role;
                                            });
        
        $select = $this->permission->join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                                        ->where("role_has_permissions.role_id",$id)
                                            ->get();
        
        foreach($select as $selec):
            $selected[$selec->name] = 'checked';

        endforeach;

        $selected = isset($selected) ? $selected : 'empty';
        $selected = collect($selected);
        return view('configs.roles.show',compact('roles','permissions', 'selected'));

    }

    
    public function edit($id)
    {
        $roles = $this->role->findOrFail($id);
        $permissions = $this->permission->distinct()->orderBy('name')->get()
                                            ->groupBy( function($item) {
                                                
                                                list($role, $action) = explode("-", $item->name);
                                                return $role;
                                            });
                                                                                
        
        $select = $this->permission->join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                                        ->where("role_has_permissions.role_id",$id)
                                            ->get();
        
        foreach($select as $selec):
            $selected[$selec->name] = 'checked';

        endforeach;

        $selected = isset($selected) ? $selected : 'empty';
        $selected = collect($selected);
        return view('configs.roles.edit',compact('roles','permissions', 'selected'));
    }

    
    public function update(Request $request, $id)
    {
        $name = $this->role->where('name',$request->input('name'))->first();
        if(isset($name->id)):
            if($name->id != $id):
                toast(trans('messages.edi_err_rule'),'error');
                return redirect()->back();

            endif;

        endif;

        try{
            $role = $this->role->findOrFail($id);
            $role->name = $request->input('name');
            $role->save();
            $role->syncPermissions($request->input('permission'));
            toast(trans('messages.edi_suc_rule'),'success');
            return redirect()->route('roles.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_rule'),'error');
            return redirect()->back();
        }
    }


 
    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';

        $roles = $this->role->findOrFail($id);
        
        $permissions = $this->permission->distinct()->orderBy('name')->get()
                                        ->groupBy( function($item) {
                                            list($role, $action) = explode("-", $item->name);
                                            return $role;
                                        });

        $select = $this->permission->join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                                    ->where("role_has_permissions.role_id",$id)
                                    ->get();
        
        foreach($select as $selec):
            $selected[$selec->name] = 'checked';

        endforeach;

        $selected = isset($selected) ? $selected : 'empty';
        $selected = collect($selected);
        
        if(isset($del)):    
            return view('configs.roles.delete',compact('roles','permissions', 'selected'));            
        
        else:
            try{
                $roles = $this->role->findOrFail($id);
                $roles->delete();
                toast(trans('messages.del_suc_rule'),'success');
                return redirect()->route('roles.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_err_rule'),'error');
                return redirect()->back();
            }        
        endif;
    }
}
