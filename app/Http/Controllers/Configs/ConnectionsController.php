<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Connection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ConnectionsController extends Controller
{

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        
    }

    
    public function index(Request $request)
    {
        
        $search = $request->input('search');

        if($search):
            $connections = $this->connection->where('name','like', '%'.$search.'%')
                                            ->orderBy('name')
                                            ->paginate(30);
        
        else:    
            $connections = $this->connection->orderBy('name')
                                            ->paginate(30);
        endif;

        return view('configs.connections.index', compact('connections','search'));
    
    }

    
    public function create()
    {
        $types = collect([
            'Arquivo' => 'Arquivo',
            'FTP'     => 'FTP',
            'TCP'     => 'TCP',
            'Telnet'  => 'Telnet',
            ]);

        return view('configs.connections.create', compact('types'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:connections',
            'type' => 'required',       
        ]);
        
        try{
            $connection = $this->connection->create($request->all());
            toast('Conexão Cadastrada com Sucesso !','success');
            return redirect()->route('connections.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar cadastrar a conexão !','error');
            return redirect()->back();
        }

    }

 
    public function show($id)
    {

    }

 
    public function edit($id)
    {
        $connection = $this->connection->findOrFail($id);
        $types = collect([
            'Arquivo' => 'Arquivo',
            'FTP'     => 'FTP',
            'TCP'     => 'TCP',
            'Telnet'  => 'Telnet',
            ]);

        return view('configs.connections.edit', compact('types','connection'));

    }

 
    public function update(Request $request, $id)
    {
        $name = $this->connection->where('name',$request->input('name'))->first();
        if(isset($name->id)):
            if($name->id != $id):
                toast('Ocorreu um erro ao tentar atualizar a conexão !','error');
                return redirect()->back();

            endif;
        endif;

        try{
            $connection = $this->connection->findOrFail($id);
            $connection->update($request->all());
            toast('Conexão atualizada com sucesso !','success');
            return redirect()->route('connections.index');

        } catch(\Exception $e) {

            if(env('APP_DEBUG')):
                toast($e->getMessage(),'error');
                return redirect()->back();
            
            endif;
            
            toast('Ocorreu um erro ao tentar atualizar a conexão !','error');
            return redirect()->back();
        }
    }

 
    public function destroy($id)
    {
        !is_numeric($id) ? list($del, $id) = explode("-", $id) : '';
        
        $connection = $this->connection->findOrFail($id);
        $types = collect([
            'Arquivo' => 'Arquivo',
            'FTP'     => 'FTP',
            'TCP'     => 'TCP',
            'Telnet'  => 'Telnet',
            ]);
        
        if(isset($del)):    
            return view('configs.connections.delete',compact('connection','types'));            
        
        else:
            try{
                $connection = $this->connection->findOrFail($id);
                $connection->delete();
                toast('Coletor excluido com sucesso!','success');    
                return redirect()->route('connections.index');
    
            } catch(\Exception $e) {
    
                if(env('APP_DEBUG')):
                    toast($e->getMessage(),'error');
                    return redirect()->back();
                
                endif;
                
                toast('Ocorreu um erro ao tentar excluir o coletor !','error');
                return redirect()->back();
            }        
        endif;
    }
}
