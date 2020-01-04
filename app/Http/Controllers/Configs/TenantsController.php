<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
class TenantsController extends Controller
{
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;        
    }

    
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if($search):
            $tenants = $this->tenant->where('tenant','like', '%'.$search.'%')
                                ->orderBy('tenant')
                                ->paginate(30);
        
        else:    
            $tenants = $this->tenant->orderBy('tenant')
                                ->paginate(30);
        endif;

        return view('configs.tenants.index', compact('tenants','search'));
    
    }


    public function create()
    {
        return view('configs.tenants.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'tenant' => 'required|alpha_dash|unique:tenants',
        ]);
        
        try{
            $tenant = $this->tenant->create($request->all());
            toast('Empresa Cadastrada com Sucesso !','success');
            return redirect()->route('tenants.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar cadastrar a Empresa !','error');
            return redirect()->back();
        }

    }


    public function show($id)
    {
        $tenant = $this->tenant->findOrFail($id);
        
        return view('configs.tenants.show', compact('tenant'));

    }


    public function edit($id)
    {
        $tenant = $this->tenant->findOrFail($id);
        
        return view('configs.tenants.edit', compact('tenant'));

    }


    public function update(Request $request, $id)
    {
        $tenant = $this->tenant->where('tenant',$request->input('tenant'))->first();
        $request->validate([
            'tenant' => 'required|alpha_dash',
        ]);

        if(isset($tenant->id)):
            if($tenant->id != $id):
                toast('Ocorreu um erro ao tentar atualizar a Empresa !','error');
                return redirect()->back();

            endif;
        endif;
        
        try{
            $tenant = $this->tenant->findOrFail($id);
            $tenant->update($request->all());
            toast('Empresa atualizada com sucesso !','success');
            return redirect()->route('tenants.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar atualizar a Empresa !','error');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $tenant = $this->tenant->findOrFail($id);
        
        if(isset($del)):    
            return view('configs.tenants.delete',compact('tenant'));            
        
        else:
            try{
                $tenant = $this->tenant->findOrFail($id);
                $tenant->delete();
                toast('Empresa excluida com sucesso!','success');    
                return redirect()->route('tenants.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast('Ocorreu um erro ao tentar excluir a Empresa !','error');
                return redirect()->back();
            }        
        endif;
    }
}
