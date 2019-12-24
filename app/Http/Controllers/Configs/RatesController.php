<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Route;
use App\Models\Prefix;


class RatesController extends Controller
{
  
    private $services = [
        'FIXO'          => 'FIXO',
        'MOVEL'         => 'MOVEL',
        'INTERNACIONAL' => 'INTERNACIONAL',
        'GRATUITO'      => 'GRATUITO',
        'SERVIÇO'       => 'SERVIÇO',
        'OUTROS'        => 'OUTROS',
        ];
    
    private $types = [
        'DDI'      => 'DDI',
        'LOCAL'    => 'LOCAL',
        'DDD'      => 'DDD',
        'VC1'      => 'VC1',
        'VC2'      => 'VC2',
        'VC3'      => 'VC3',
        'SERVIÇOS' => 'SERVIÇOS',
        'OUTROS'   => 'OUTROS',
        'GRATUITO' => 'GRATUITO',
        'TIE_LINE' => 'TIE_LINE',
        ];

    private $directions = [
        'IC' => 'IC',
        'OC' => 'OC',
        'IN' => 'IN',
        'TL' => 'TL',
        ];

        public function __construct(Rate $rate, Route $route, Prefix $prefix)
    {
        $this->rate = $rate;        
        $this->route = $route;        
        $this->prefix = $prefix;        
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        $services = collect($this->services);
        
        if($search):
            $rates = $this->rate->where('rname','like', '%'.$search.'%')
                                ->orderBy('rname')
                                ->paginate(30);
        
        else:    
            $rates = $this->rate->orderBy('rname')
                                ->paginate(30);
        endif;

        return view('configs.rates.index', compact('rates','search','services'));
    
    }

    public function create()
    {
        $services  = collect($this->services);
        $directions  = collect($this->directions);
        $types     = collect($this->types);
        $routes    = $this->route->get();
        return view('configs.rates.create', compact('services','routes','types', 'directions'));

    }

    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'rname'            => 'required|unique:rates',
            'routes_route'     => "required|unique:rates,routes_route,NULL,id,prefixes_service,$request->prefixes_service,type,$request->type,direction,$request->direction",       
            'prefixes_service' => 'required',       
            'type'             => 'required',       
            'direction'        => 'required',       
            'rate'             => 'required|numeric',       
            'connection'       => 'nullable|numeric',       
            'stime'            => 'required|numeric',       
            'ttmin'            => 'required|numeric',       
            'increment'        => 'required|numeric',       
            
            //'name' => 'unique:table,field,NULL,id,field1,value1,field2,value2,field3,value3'
            //'|unique:trunks,trunk,NULL,id,tpbx,' . $request->tpbx, 
        ]);
        
        try{
            $rate = $this->rate->create($request->all());
            toast('Tarifa Cadastrada com Sucesso !','success');
            return redirect()->route('rates.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar cadastrar a tarifa !','error');
            return redirect()->back();
        }
        
        
    }

    public function show($id)
    {
        $rate = $this->rate->findOrFail($id);
        $services  = collect($this->services);
        $directions  = collect($this->directions);
        $types     = collect($this->types);
        $routes    = $this->route->get();
        return view('configs.rates.show', compact('services','routes','types', 'directions','rate'));

    }

    public function edit($id)
    {
        $rate = $this->rate->findOrFail($id);
        $services  = collect($this->services);
        $directions  = collect($this->directions);
        $types     = collect($this->types);
        $routes    = $this->route->get();
        return view('configs.rates.edit', compact('services','routes','types', 'directions','rate'));

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rname'            => 'required',
            'routes_route'     => "required",       
            'prefixes_service' => 'required',       
            'type'             => 'required',       
            'direction'        => 'required',       
            'rate'             => 'required|numeric',       
            'connection'       => 'nullable|numeric',       
            'stime'            => 'required|numeric',       
            'ttmin'            => 'required|numeric',       
            'increment'        => 'required|numeric',       
        ]);

        $rname = $this->rate->where('rname',$request->input('rname'))->first();
        $rate = $this->rate->where('routes_route',$request->input('routes_route'))
                            ->where('prefixes_service',$request->input('prefixes_service'))
                            ->where('type',$request->input('type'))
                            ->where('direction',$request->input('direction'))
                            ->first();
                            
        if(isset($rate->id)):
            if($rate->id != $id):
                toast('Ocorreu um erro ao tentar atualizar a tarifa !','error');
                $request->validate([
                    'routes_route' => "required|unique:rates,routes_route,NULL,id,prefixes_service,$request->prefixes_service,type,$request->type,direction,$request->direction",       
                ]);
                return redirect()->back();
            endif;
        endif;
            
        if(isset($rname->id)):
            if($rname->id != $id):
                toast('Ocorreu um erro ao tentar atualizar a tarifa !','error');
                $request->validate([
                    'rname'            => 'required|unique:rates',
                ]);
                return redirect()->back();
            endif;
        endif;

        try{
            $rate = $this->rate->findOrFail($id);
            $rate->update($request->all());
            toast('Tarifa atualizada com sucesso !','success');
            return redirect()->route('rates.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar atualizar a tarifa !','error');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        //
    }
}
