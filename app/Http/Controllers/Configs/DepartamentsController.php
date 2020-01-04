<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departament;
use App\Models\Section;

class DepartamentsController extends Controller
{
    public function __construct(Departament $departament, Section $section)
    {
        $this->departament = $departament;        
        $this->section = $section;        
    }

    public function index(Request $request)
    {
        
        $search = $request->input('search');
        
        if($search):
            $departaments = $this->departament->where('departament','like', '%'.$search.'%')
                                ->orderBy('departament')
                                ->paginate(30);
        
        else:    
            $departaments = $this->departament->orderBy('departament')
                                ->paginate(30);
        endif;

        return view('configs.departaments.index', compact('departaments','search'));
    
    }
 

    public function create()
    {
        $sections    = $this->section->get();
        return view('configs.departaments.create', compact('sections'));

    }


    public function store(Request $request)
    {

        $request->validate([
            'departament' => 'required|unique:departaments,departament,NULL,id,sections_id,' . $request->sections_id, 
            'sections_id' => 'required',       
        ]);
        
        try{
            $departament = $this->departament->create($request->all());
            toast('Seção Cadastrada com Sucesso !','success');
            return redirect()->route('departaments.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar cadastrar o Departamento !','error');
            return redirect()->back();
        }
    }


    public function show($id)
    {
        $departament  = $this->departament->findOrFail($id);
        $sections = $this->section->get();

        return view('configs.departaments.show', compact('departament','sections'));

    }


    public function edit($id)
    {
        $departament  = $this->departament->findOrFail($id);
        $sections = $this->section->get();

        return view('configs.departaments.edit', compact('departament','sections'));
    }


    public function update(Request $request, $id)
    {
        
        $departament = $this->departament->where('departament',$request->input('departament'))->where('sections_id',$request->input('sections_id'))->first();
        
        $request->validate([
            'departament' => 'required', 
            'sections_id' => 'required',      
        ]);

        if(isset($departament->id)):
            if($departament->id != $id):
                toast('Ocorreu um erro ao tentar atualizar o Departamento !','error');
                $request->validate([
                    'departament' => 'unique:departaments,departament,NULL,id,sections_id,' . $request->sections_id, 
                ]);
                
                return redirect()->back();

            endif;
        endif;
        
        try{
            $departament = $this->departament->findOrFail($id);
            $departament->update($request->all());
            toast('Departamento atualizado com sucesso !','success');
            return redirect()->route('departaments.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar atualizar o Departamento !','error');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $departament  = $this->departament->findOrFail($id);
        $sections = $this->section->get();

        if(isset($del)):    
            return view('configs.departaments.delete', compact('departament','sections'));

        else:
            try{
                $departament  = $this->departament->findOrFail($id);
                $departament->delete();
                toast('Departamento Excluido com sucesso!','success');    
                return redirect()->route('departaments.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast('Ocorreu um erro ao tentar excluir o Departamento !','error');
                return redirect()->back();
            }        
        endif;
    }

}
