<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pbx;
use App\Models\Call;
use App\Models\Extension;


class ByExtensionsController extends Controller
{

    public function __construct(Pbx $pbx, Call $call, Extension $extension)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
        $this->extension = $extension;        
    }

    public function index()
    {
        $extensions = $this->extension->select('extension', 'ename','pbxes_id')->get()->groupBY('pbxes_id');
        
        return view('reports.byextensions.index', compact('extensions'));
    }

   
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        $start_datetime = $request->input('start_date').' '.$request->input('start_time');
        $end_datetime   = $request->input('end_date').' '.$request->input('end_time');
        $extensions = $request->input('extensions');
        $directions = $request->input('directions');
        $dialNumber = $request->input('dialNumber');
        $types=$request->input('types');
        $types[]='INT';
        
        $request->merge([
            'start_date' => $start_datetime,
            'end_date'   => $end_datetime,
            'types'   => $types,
        ]);

        $request->validate([
            
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'extensions' => 'required|min:1',
            'dialNumber' => 'nullable|numeric',
            'directions' => 'required|min:1',
            'types' => 'required|min:1',
        ]);

        $extens = $this->extension->select('extension','ename')->get()->groupBy('extension');
        $calls = $this->call->select('calls.id AS cid', '*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->whereBetween('calldate', [$start_datetime, $end_datetime])
                                ->whereIn('extensions_id',$extensions)
                                ->whereIn('direction',$directions)
                                ->whereIn('cservice',$types)
                                ->where('dialnumber','like', '%'.$dialNumber.'%')
                                ->where('status_id','1')
                                ->orderBy('extensions_id','asc')
                                ->orderBy('calldate','asc')
                                ->get()
                                ->groupBy([
                                    'extensions_id',
                                    function ($item) {
                                        return date('d/m/Y', strtotime($item->calldate));
                                    }

                                ], $preserveKeys = true);
                                
        //$extens = json_decode(json_encode($extens));
        //dd($extens);
        
        return view('reports.byextensions.report', compact('calls','extens'));
    }

   
    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        //
    }

   
    public function update(Request $request, $id)
    {
        //
    }

   
    public function destroy($id)
    {
        //
    }
}
