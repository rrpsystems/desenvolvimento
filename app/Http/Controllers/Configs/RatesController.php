<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Route;
use App\Models\Prefix;


class RatesController extends Controller
{
    
        public function __construct(Rate $rate, Route $route, Prefix $prefix)
    {
        $this->rate = $rate;        
        $this->route = $route;        
        $this->prefix = $prefix;        
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        $services = services();
        
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
        $services  = services();
        $directions  = directions();
        $types     = types();
        $routes    = $this->route->get();
        return view('configs.rates.create', compact('services','routes','types', 'directions'));

    }

    public function store(Request $request)
    {
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
            
        ]);
        
        try{
            $rate = $this->rate->create($request->all());
            toast(trans('messages.cad_suc_rate'),'success');
            return redirect()->route('rates.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_rate'),'error');
            return redirect()->back();
        }
        
        
    }

    public function show($id)
    {
        $rate = $this->rate->findOrFail($id);
        $services  = services();
        $directions  = directions();
        $types     = types();
        $routes    = $this->route->get();
        return view('configs.rates.show', compact('services','routes','types', 'directions','rate'));

    }

    public function edit($id)
    {
        $rate = $this->rate->findOrFail($id);
        $services  = services();
        $directions  = directions();
        $types     = types();
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
                toast(trans('messages.edi_err_rate'),'error');
                $request->validate([
                    'routes_route' => "required|unique:rates,routes_route,NULL,id,prefixes_service,$request->prefixes_service,type,$request->type,direction,$request->direction",       
                ]);
                return redirect()->back();
            endif;
        endif;
            
        if(isset($rname->id)):
            if($rname->id != $id):
                toast(trans('messages.edi_err_rate'),'error');
                $request->validate([
                    'rname'            => 'required|unique:rates',
                ]);
                return redirect()->back();
            endif;
        endif;

        try{
            $rate = $this->rate->findOrFail($id);
            $rate->update($request->all());
            toast(trans('messages.edi_suc_rate'),'success');
            return redirect()->route('rates.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_rate'),'error');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        //
    }
}
