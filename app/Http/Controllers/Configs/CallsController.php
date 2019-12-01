<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Pbx;
use App\Models\Call;


class CallsController extends Controller
{
    public function __construct(Pbx $pbx, Call $call)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        if($search):
            $calls = $this->pbx->where('dialnumber','like', '%'.$search.'%')
                                ->orderBy('calldate', 'DESC')
                                ->paginate(30);
        
        else:    
            $calls = $this->call->orderBy('calldate', 'DESC')
                                ->paginate(30);
        endif;

        return view('informations.calls.index', compact('calls','search'));
    
        //$calls = $this->call->get();
        //dd($calls);

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
