<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\User;
use App\DB;

class UsersController extends Controller
{

    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }


    public function index(Request $request)
    {
        $search = $request->input('search');

        if($search):
            $users = $this->user->select('users.id as id', 'users.name', 'email', 'roles.name as role')
                                ->leftJoin('model_has_roles', 'model_id', '=', 'users.id' )
                                ->leftJoin('roles', 'roles.id', '=', 'role_id' )
                                ->where('users.name','like', '%'.$search.'%')
                                ->orderBy('users.name')
                                ->whereNotIn('email', ['root@root.com'])
                                ->paginate(30);
        
        else:    
            $users = $this->user->select('users.id as id', 'users.name', 'email', 'roles.name as role')
                                ->leftJoin('model_has_roles', 'model_id', '=', 'users.id' )
                                ->leftJoin('roles', 'roles.id', '=', 'role_id' )
                                ->orderBy('users.name')
                                ->whereNotIn('email', ['root@root.com'])
                                ->paginate(30);
        endif;
        return view('configs.users.index', compact('users','search'));
    
    }

  
    public function create()
    {

        $roles = $this->role->pluck('name','name')->all();

        return view('configs.users.create', compact('roles'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'role' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        
        try{
            $user = $this->user->create($request->all());
            $user->assignRole($request->role);
            toast('Usuario Cadastrado com Sucesso !','success');
            return redirect()->route('users.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar cadastrar o Usuario !','error');
            return redirect()->back();
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $roles = $this->role->pluck('name','name')->all();
        $user = $this->user->select('users.id as id', 'users.name', 'email', 'roles.name as role')
                            ->leftJoin('model_has_roles', 'model_id', '=', 'users.id' )
                            ->leftJoin('roles', 'roles.id', '=', 'role_id' )
                            ->where('users.id',$id)
                            ->firstOrFail();

        return view('configs.users.edit',compact('roles','user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'role' => 'required',
            'password' => 'nullable|confirmed|min:6',
            
        ]);
        
        try{
            $user = $this->user->findOrFail($id);
            $user->update($request->filled('password') ? $request->all() : $request->except(['password']));
            $user->syncRoles($request->role);            
            toast('Usuario Editado com Sucesso !','success');
            return redirect()->route('users.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar editar o Usuario !','error');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';

        $roles = $this->role->pluck('name','name')->all();
        $user = $this->user->select('users.id as id', 'users.name', 'email', 'roles.name as role')
                            ->leftJoin('model_has_roles', 'model_id', '=', 'users.id' )
                            ->leftJoin('roles', 'roles.id', '=', 'role_id' )
                            ->where('users.id',$id)
                            ->firstOrFail();
       
        
        if(isset($del)):    
            return view('configs.users.delete',compact('roles','user'));            
        
        else:
            try{
                $user = $this->user->findOrFail($id);
                $user->delete();
                toast('Usuario Excluido com sucesso!','success');    
                return redirect()->route('users.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast('Ocorreu um erro ao tentar excluir o usuario !','error');
                return redirect()->back();
            }        
        endif;

    }
}
