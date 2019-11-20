<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Alert;


class RolesController extends Controller
{
    //function __construct()
    //{
        //$this->middleware('permission:conf-role-list');
        //$this->middleware('permission:conf-role-create', ['only' => ['create','store']]);
        //$this->middleware('permission:conf-role-edit', ['only' => ['edit','update']]);
        //$this->middleware('permission:conf-role-delete', ['only' => ['destroy']]);
    //}
    public function __construct(Role $role)
    {
        $this->role = $role;
    }





    public function index()
    {
        toast('Cadastro realizado com sucesso!','success');
        $roles = $this->role->orderBy('id')
            ->paginate(100);
        //dd($roles);
      return view('configs.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
