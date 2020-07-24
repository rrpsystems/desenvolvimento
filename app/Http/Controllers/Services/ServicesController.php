<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Pbx;
use App\Models\Prefix;
use App\Models\Call;
use App\Models\Rate;
use App\Models\Trunk;
use App\Models\Route;

class ServicesController extends Controller
{
    public function __construct(Pbx $pbx, Call $call, Prefix $prefix, Rate $rate, Trunk $trunk, Route $route)
    {
        $this->pbx = $pbx;
        $this->prefix = $prefix;
        $this->call = $call;
        $this->rate = $rate;
        $this->trunk = $trunk;
        $this->route = $route;
        
    }

    public function test()
    {
     
        $now = strtotime(date('Y-m-d H:i:s'));
        $calldate = strtotime(date('2019-11-30 21:22:00'));
        
        if($now < $calldate):
            $calldate =  date('Y-m-d H:i:s', strtotime('-1 year', $calldate));
        else:
            $calldate =  date('Y-m-d H:i:s', strtotime('0 days',$calldate));
        endif;
        
        dd($calldate);
     
        //pega os arquivos na pasta e importa para o banco de dados de acordo com o modelo do pbx.
            $allfiles = Storage::disk('local')->files('bilhetes/REAL_SERV_IMPACTA');
            
            foreach ($allfiles as $file):
                //Chama a função no Helpers/de acordo com o fabricante/modelo do PBX.
                dd(intelbras_impacta($file,'REAL_SERV_IMPACTA'));
               
            endforeach;

    }
    //Coleta os bilhetes do PBX
    public function collector()
    {
        $conns = $this->pbx->whereNotNull('host')->get();
        
        foreach($conns as $conn):
            //Chama a função no arquivo Helpers/connections.php de acordo com a conexão.
            trim(strtolower($conn->connection))($conn->name, $conn->host, $conn->port, $conn->user, $conn->password); 
        endforeach;
    
    }

    public function import()
    {    
        //set_time_limit(500);
        $imports = $this->pbx->get();
        
        foreach($imports as $import):
            //pega os arquivos na pasta e importa para o banco de dados de acordo com o modelo do pbx.
            $allFiles = Storage::disk('local')->files('bilhetes/'.$import->name);
            $allfiles = preg_grep('/.cdr/', $allFiles); //filtra os arquivos com a extensão cdr

            foreach ($allfiles as $file):
                //Chama a função no Helpers/de acordo com o fabricante/modelo do PBX.
                $success = trim(strtolower($import->model))($file,$import->name);
                if($success):
                    mv_file($import->name,$file);
                endif; 
            endforeach;
        endforeach;

    }

    public function billing()
    {
        set_time_limit(180);
        //Seleciona as ligações para a tarifação
        $calls = $this->call->select('calls.id AS cid', '*')
                            ->leftJoin('trunks','trunk','trunks_id')
                            ->leftJoin('routes', 'trunks.routes_route', 'route')
                            ->where('status_id','0')
                            ->orderBy('calldate','DESC')
                            ->limit(5000)
                            ->get();
        
        foreach($calls as $call):
            //Verifica se Possui um tronco cadastrado
            if($call->callnumber == 'error_92'):
                $update = $this->call->where('id',$call->cid)
                                    ->update([
                                            'rate'       => '0', 
                                            'callnumber' => '',
                                            'status_id'  => '92'
                                        ]);
                    continue;
            endif;

            //verifica se possui um prefixo cadastrado para o numero
            $prefix = $this->prefix->whereRaw( "'$call->callnumber' LIKE CONCAT(prefix,'%')" )
                                    ->orderByRaw('length(prefix) DESC')->first();
            //dd($prefix);
            //se não possuir Prefixo cadastrado insere o erro 91
            if(!$prefix):
                if($call->direction == 'IN'):
                    $update = $this->call->where('id',$call->cid)
                                    ->update([
                                            'rate'      => '0', 
                                            'cservice'      => 'INT', 
                                            'status_id' => '1'
                                        ]);
                    continue;
                
                elseif($call->prefix_id == 'NI' && $call->direction == 'IC'):
                    $update = $this->call->where('id',$call->cid)
                                    ->update([
                                            'rate'      => '0', 
                                            'cservice'      => 'NI', 
                                            'status_id' => '1'
                                        ]);
                    continue;
                
                else:
                    $update = $this->call->where('id',$call->cid)
                                        ->update(['status_id' => '91']);
                    continue;
                
                endif;
            endif;

            //verifica o tipo de serviço do prefixo e do numero
            switch($prefix->service):
                case'STFC':
                    if($call->ddd == substr($prefix->prefix,0,4)):
                        $type = 'LOCAL';
                    else:
                        $type = 'LDN';
                    endif;
                break;
                case'SMP':
                    if($call->ddd == substr($prefix->prefix,0,4)):
                        $type = 'VC1';    
                    elseif(substr($call->ddd,0,3) == substr($prefix->prefix,0,3)):
                        $type = 'VC2';
                    else:
                        $type = 'VC3';
                    endif;
                break;         
                case'DDI':
                    $type = 'LDI';
                break; 
                case'GRATUITO':
                    $type = 'GRATUITO';
                break;      
                case'SERVIÇO':
                    $type = 'SERVIÇO';
                break;       
                case'OUTROS':
                    $type = 'OUTROS';
                break;        
            endswitch;

            //verifica se possui uma tarifa cadastrada para o serviço
            $rate = $this->rate->where('type',$type)
                                ->where('routes_route', $call->route)
                                ->where('prefixes_service', $prefix->service)
                                ->where('direction', $call->direction)
                                ->first();

            // Verifica se Possui uma Tarifa cadastrada
            if(!$rate):
                // caso a ligação seja diferente de saida insere os dados como tarifados porem sem valor
                if($call->direction != 'OC'):
                    $update = $this->call->where('id',$call->cid)
                                        ->update([
                                            'prefixes_id' => $prefix->prefix, 
                                            'cservice'    => $type, 
                                            'rate'        => '0', 
                                            'status_id'   => '1'
                                        ]);
                    continue;
                else:
                    //se não possuir uma tarifa cadastrada insere o erro 92 para chamadas de saida
                    $update = $this->call->where('id',$call->cid)
                                        ->update(['status_id' => '92']);
                    continue;
                endif;
            endif;
            
            if($call->billsec <= $rate->stime):
                $value = ( (float)0.00 + (float)$rate->connection);
                $update = $this->call->where('id',$call->cid)
                                    ->update([
                                        'prefixes_id' => $prefix->prefix, 
                                        'cservice'    => $type, 
                                        'rates_id'    => $rate->rname, 
                                        'rate'        => $value, 
                                        'status_id'   => '1'
                                    ]);
                continue;

            elseif ($call->billsec >= $rate->stime && $call->billsec <= $rate->ttmin):
                $value = (($rate->rate / 60 ) * $rate->ttmin) + $rate->connection;
                $value = (ceil($value * 100) / 100);
                $update = $this->call->where('id',$call->cid)
                                    ->update([
                                            'prefixes_id' => $prefix->prefix, 
                                            'cservice'    => $type, 
                                            'rates_id'    => $rate->rname, 
                                            'rate'        => $value, 
                                            'status_id'   => '1'
                                        ]);
                continue;

            else:
                $time = ceil(($call->billsec - $rate->ttmin) / $rate->increment);
                $calc = ((($rate->rate / 60 ) * $rate->increment) * $time) + ((($rate->rate / 60 ) * $rate->ttmin) + $rate->connection);
                $value = (ceil($calc * 100) / 100);
                $update = $this->call->where('id',$call->cid)
                                    ->update([
                                        'prefixes_id' => $prefix->prefix, 
                                        'cservice'    => $type, 
                                        'rates_id'    => $rate->rname, 
                                        'rate'        => $value, 
                                        'status_id'   => '1'
                                    ]);
                continue;
            endif;
        endforeach;   
    }
}
