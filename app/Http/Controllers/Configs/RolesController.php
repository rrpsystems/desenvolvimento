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


    public function index()
    {
        //alerta na pagina principal
        //toast('Cadastro realizado com sucesso!','success');
        $roles = $this->role->orderBy('id')
            ->paginate(100);
            
      return view('configs.roles.index', compact('roles'));
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

            toast('Permissão cadastrada com sucesso!','success');

            return redirect()->route('roles.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
            
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar cadastrar a permissão!','error');
            return redirect()->back();
        }
    }


    public function show($id)
    {
        
        $roles = $this->role->find($id);

        $permissions = $this->permission->distinct()->get()
                                            ->groupBy( function($item) {
                                                list($role, $action) = explode("-", $item->name);
                                                return $role;
                                            //return $item->created_at->format('Y-m-d');
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
        $roles = $this->role->find($id);

        $permissions = $this->permission->distinct()->get()
                                            ->groupBy( function($item) {
                                                list($role, $action) = explode("-", $item->name);
                                                return $role;
                                            //return $item->created_at->format('Y-m-d');
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
        //
    }


 
    public function destroy($id)
    {
        //dd($request);
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
       
        
        $roles = $this->role->find($id);
        
        $permissions = $this->permission->distinct()->get()
        ->groupBy( function($item) {
            list($role, $action) = explode("-", $item->name);
            return $role;
            //return $item->created_at->format('Y-m-d');
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

                DB::table("roles")->where('id',$id)->delete();
                toast('Permissão Excluida com sucesso!','success');
    
                return redirect()->route('roles.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast('Ocorreu um erro ao tentar excluir a permissão!','error');
                return redirect()->back();
            }
        
        endif;
    }
}
