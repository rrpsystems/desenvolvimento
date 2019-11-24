<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\User;


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
                                ->paginate(100);
        
        else:    
            $users = $this->user->select('users.id as id', 'users.name', 'email', 'roles.name as role')
                                ->leftJoin('model_has_roles', 'model_id', '=', 'users.id' )
                                ->leftJoin('roles', 'roles.id', '=', 'role_id' )
                                ->orderBy('users.name')
                                ->paginate(100);
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

        if($user):
            return redirect()
                    ->route('users.index', ['search' => $request->name])
                        ->with('msg', sendMsgSuccess('O Usuario <b>'.$request->name.'</b> foi cadastrado com Sucesso !'));
        endif;

        return redirect()
                ->route('users.index', ['search' => $request->name])
                    ->with('msg', sendMsgDanger('Ocorreu um erro ao salvar o usuario -> <b>'.$request->name.'</b> !'));

        return back()->withInput();
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

            //dd($user);
            //dd($roles);
        return view('configs.users.edit',compact('roles','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
