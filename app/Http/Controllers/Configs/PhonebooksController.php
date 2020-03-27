<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Phonebook;

class PhonebooksController extends Controller
{
    public function __construct(Phonebook $phonebook)
    {
        $this->phonebook = $phonebook;        
    }

    
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if($search):
            $phonebooks = $this->phonebook
                                ->where('phonenumber','like', '%'.$search.'%')
                                ->orWhere('phonename','like', '%'.$search.'%')
                                ->orderBy('phonenumber')
                                ->paginate(30);
        
        else:    
            $phonebooks = $this->phonebook->orderBy('phonenumber')
                                ->paginate(30);
        endif;

        return view('configs.phonebooks.index', compact('phonebooks','search'));
    
    }

    
    public function create()
    {
        return view('configs.phonebooks.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'phonenumber' => 'required|numeric|unique:phonebooks',
            'phonename'   => 'required',
        ]);
        
        try{
            $phonebook = $this->phonebook->create($request->all());
            toast(trans('messages.cad_suc_phonebook'),'success');
            return redirect()->route('phonebooks.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.cad_err_phonebook'),'error');
            return redirect()->back();
        }

    }

    
    public function show($id)
    {
        $phonebook = $this->phonebook->findOrFail($id);
        
        return view('configs.phonebooks.show', compact('phonebook'));
    }

    
    public function edit($id)
    {
        $phonebook = $this->phonebook->findOrFail($id);
        
        return view('configs.phonebooks.edit', compact('phonebook'));
    }

    
    public function update(Request $request, $id)
    {
        $phonebook = $this->phonebook->where('phonenumber',$request->input('phonenumber'))->first();
        $request->validate([
            'phonenumber' => 'required|numeric',
        ]);

        if(isset($phonebook->id)):
            if($phonebook->id != $id):
                toast(trans('messages.edi_err_phonebook'),'error');
                return redirect()->back();

            endif;
        endif;
        
        try{
            $phonebook = $this->phonebook->findOrFail($id);
            $phonebook->update($request->all());
            toast(trans('messages.edi_suc_phonebook'),'success');
            return redirect()->route('phonebooks.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast(trans('messages.edi_err_phonebook'),'error');
            return redirect()->back();
        }
    }

    
    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $phonebook = $this->phonebook->findOrFail($id);
        
        if(isset($del)):    
            return view('configs.phonebooks.delete',compact('phonebook'));            
        
        else:
            try{
                $phonebook = $this->phonebook->findOrFail($id);
                $phonebook->delete();
                toast(trans('messages.del_suc_phonebook'),'success');
                return redirect()->route('phonebooks.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast(trans('messages.del_err_phonebook'),'error');
                return redirect()->back();
            }        
        endif;
    }

}
