<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Pbx;
use App\Models\Prefix;

class RoutesController extends Controller
{
    
    private $dials = [
        'Pais + DDD + Telefone'             => 'Pais + DDD + Telefone',
        '0 + Operadora + DDD + Telefone'    => '0 + Operadora + DDD + Telefone',
        '0 + DDD + Telefone'                => '0 + DDD + Telefone',
        'DDD + Telefone'                    => 'DDD + Telefone',
        ];

    public function __construct(Route $route, Pbx $pbx)
    {
        $this->route = $route;        
        $this->pbx = $pbx;        
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if($search):
            $routes = $this->route->where('route','like', '%'.$search.'%')
                                ->orderBy('route')
                                ->paginate(30);
        
        else:    
            $routes = $this->route->orderBy('route')
                                ->paginate(30);
        endif;

        return view('configs.routes.index', compact('routes','search'));
    
    }

    
    public function create()
    {
        $dials    = collect($this->dials);
        $pbxes    = $this->pbx->get();
        return view('configs.routes.create', compact('pbxes','dials'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'route' => 'required|unique:routes,route,NULL,id,rpbx,' . $request->rpbx, 
            'rpbx' => 'required',       
            'ddd' => 'required',       
            'dialplan' => 'required',       
        ]);
            //dd($request->all());
        
        try{
            $route = $this->route->create($request->all());
            toast('Rota Cadastrada com Sucesso !','success');
            return redirect()->route('routes.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar cadastrar a Rota !','error');
            return redirect()->back();
        }
    }

    
    public function show($id)
    {
        $route = $this->route->findOrFail($id);
        $dials    = collect($this->dials);
        $pbxes    = $this->pbx->get();
        return view('configs.routes.show', compact('route','pbxes','dials'));

    }

    
    public function edit($id)
    {
        $route = $this->route->findOrFail($id);
        $dials    = collect($this->dials);
        $pbxes    = $this->pbx->get();
        return view('configs.routes.edit', compact('route','pbxes','dials'));

    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $route = $this->route->where('route',$request->input('route'))->where('rpbx',$request->input('rpbx'))->first();
        $request->validate([
            'route' => 'required', 
            'rpbx' => 'required',       
            'ddd' => 'required',       
            'dialplan' => 'required',      
        ]);

        if(isset($route->id)):
            if($route->id != $id):
                toast('Ocorreu um erro ao tentar atualizar a Rota !','error');
                $request->validate([
                    'route' => 'required|unique:routes,route,NULL,id,rpbx,' . $request->rpbx, 
                ]);
                
                return redirect()->back();

            endif;
        endif;
        
        try{
            $route = $this->route->findOrFail($id);
            $route->update($request->all());
            toast('Rota atualizada com sucesso !','success');
            return redirect()->route('routes.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar atualizar a Rota !','error');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $route = $this->route->findOrFail($id);
        $dials    = collect($this->dials);
        $pbxes    = $this->pbx->get();
    
        if(isset($del)):    
            return view('configs.routes.delete', compact('route','pbxes','dials'));

        else:
            try{
                $route  = $this->route->findOrFail($id);
                $route->delete();
                toast('Rota excluida com sucesso!','success');    
                return redirect()->route('routes.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast('Ocorreu um erro ao tentar excluir a Rota !','error');
                return redirect()->back();
            }        
        endif;
    }
    
}
