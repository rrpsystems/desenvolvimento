<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Pbx;
use App\Models\Call;
use App\Models\Prefix;
use App\Models\Phonebook;


class CallsController extends Controller
{
    public function __construct(Pbx $pbx, Call $call, Prefix $prefix, Phonebook $phonebook)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
        $this->prefix = $prefix;        
        $this->phonebook = $phonebook;        
    }


    public function index(Request $request)
    {
        
        $search = $request->input('search');
        if($search):
            $calls = $this->call->select('calls.id AS cid', (DB::raw(
                                "(SELECT phonename FROM phonebooks WHERE callnumber LIKE CONCAT(phonenumber,'%') ORDER BY length(phonenumber) DESC LIMIT 1) AS phonename "
                                )), '*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->where('dialnumber','like', '%'.$search.'%')
                                ->orderBy('calldate', 'DESC')
                                ->paginate(50);
        
        else:    
            $calls = $this->call->select('calls.id AS cid',(DB::raw(
                "(SELECT phonename FROM phonebooks WHERE callnumber LIKE CONCAT(phonenumber,'%') ORDER BY length(phonenumber) DESC LIMIT 1) AS phonename "
                )), '*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->where('pbx','NSX1000_VALPAMED')
                                ->orderBy('calldate', 'DESC')
                                ->paginate(50);
        endif;
        //dd($calls);
        return view('informations.calls.index', compact('calls','search'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
