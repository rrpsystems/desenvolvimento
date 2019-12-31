<?php


    function dialNumber($prefixes,$phone){

    foreach($prefixes as $prefix):
        if(substr($prefix->prefix,0,2) == 55):
            $p = substr($prefix->prefix, 2);
            $d = $prefix->dadd;
        
        else:
            $p = $prefix->prefix;
            $d = $prefix->dadd;
        
        endif;
        if(strlen($phone) == 8 || strlen($phone) == 9 ):
            $phone = "13$phone";
        endif;

        if(preg_match("/(".$p.")(\d{".$d."})/", $phone,$m)):
            $callnumber = $m['0'] ?? '';
            $prefixes_id = $prefix->prefix ?? '';
        //dd($m);
            return ['callnumber' => "55$callnumber", 'prefixes_id' => $prefixes_id, 'status_id' => '10'];
        
        endif;
    endforeach;

    return ['status_id' => '90'];
}

//Ligações de Saida //Telefone, Tipo Discagem, DDD do Tronco, Digitos Removidos
function dialOc($number, $trunk, $pbx)
{
    $drm=NULL; $dap=NULL; $tddd=NULL;
    
    $trunk = App\Models\Trunk::select('ddd','drm','dap','dialplan')
                                ->leftJoin('routes', 'routes_route', '=', 'route')
                                ->where('trunk',$trunk)
                                ->where('tpbx',$pbx)
                                ->first();
    if($trunk):
        $dialplan = $trunk->dialplan;    
        $tddd     = $trunk->ddd;
        $drm      = $trunk->drm;    
        $dap      = $trunk->dap;    
    else:
        return NULL;
    endif;
    
    
    $number = $dap . substr($number,$drm);
    
    switch($dialplan):
        
        case 'Pais + DDD + Telefone':
            
            if (preg_match("/[+]/", substr($number, 0, 1))):
                $phone = substr($number, 1);
                return $phone;
            
            else:
                return $phone;
            
            endif;
            return false;
        break;

        case '0 + Operadora + DDD + Telefone':
            
            //ligações DDI
            if (preg_match("/00[1-9][1-9][1-9]/", substr($number, 0, 5))):
                $phone = substr($number, 4);
                return $phone;

            //ligações DDD
            elseif (preg_match("/0[1-9][1-9][1-9][1-9]/", substr($number, 0, 5))):
                $phone = substr($tddd, 0,2) . substr($number, 3);
                return $phone;
            
            //Ligações 0X00
            elseif (preg_match("/0[1-9]00/", substr($number, 0, 4))):
                $phone = substr($tddd, 0,2) . substr($number, 1);
                return $phone;
            
            //Ligações Locais
            elseif (preg_match("/[2-9]/", substr($number, 0, 1))):
                $phone =  $tddd . $number;
                return $phone;
            
            //Ligações Serviços 1XX
            elseif (preg_match("/[1]/", substr($number, 0, 1))):
                $phone =  substr($tddd, 0,2) . $number;
                return $phone;
            
            endif;

            return null;
        break;

        case '0 + DDD + Telefone':
            
            //Ligações DDI
            if (preg_match("/00[1-9]/", substr($number, 0, 3))):
                $phone = substr($number, 2);
                return $phone;
            
            //Ligações DDD
            elseif (preg_match("/0[1-9][1-9][1-9]/", substr($number, 0, 4))):
                $phone = substr($tddd, 0,2) . substr($number, 1);
                return $phone;
            
            //Ligações 0X00
            elseif (preg_match("/0[1-9]00/", substr($number, 0, 4))):
                $phone = substr($tddd, 0,2) . substr($number,1);
                return $phone;
            
            //Ligações Locais
            elseif (preg_match("/[2-9]/", substr($number, 0, 1))):
                $phone =  $tddd . $number;
                return $phone;
            
            //Ligações Serviços 1XX
            elseif (preg_match("/[1]/", substr($number, 0, 1))):
                $phone =  substr($tddd, 0,2) . $number;
                return $phone;
            
            endif;
            return false;
        break;

        case 'DDD + Telefone':
            
            //Ligações DDI
            if (preg_match("/00[1-9]/", substr($number, 0, 3))):
                $phone = substr($number, 2);
                return $phone;
            
            //Ligações DDD
            elseif (preg_match("/[1-9][1-9][1-9]/", substr($number, 0, 3))):
                $phone = substr($tddd, 0,2) . $number;
                return $phone;
            
            //Ligações 0X00
            elseif (preg_match("/0[1-9]00/", substr($number, 0, 4))):
                $phone = substr($tddd, 0,2) . substr( $number,1 );
                return $phone;
            
            //Ligações Locais
            elseif (preg_match("/[2-9]/", substr($number, 0, 1))):
                $phone =  $tddd . $number;
                return $phone;
            
            //Ligações Serviços 1XX
            elseif (preg_match("/[1]/", substr($number, 0, 1))):
                $phone =  substr($tddd, 0,2) . $number;
                return $phone;
            
            endif;
            return false;
        break;

        default:
            return false;
    endswitch;

}

//Ligações de Entrada
function dialIc($number, $trunk, $pbx)
{
    $drm=NULL; $dap=NULL; $tddd=NULL;
    $trunk = App\Models\Trunk::select('ddd','drm','dap')
                                ->leftJoin('routes', 'routes_route', '=', 'route')
                                ->where('trunk',$trunk)
                                ->where('tpbx',$pbx)
                                ->first();
    if($trunk):
        $drm = $trunk->drm;    
        $dap = $trunk->dap;    
        $tddd = $trunk->ddd;    
    endif;

    $number = $dap . substr($number,$drm);
    
    if (preg_match('/^(?:(?:\+|00)?(55)\s?)?(?:\(?([0-0]?[0-9]{1}[0-9]{1})\)?\s?)??(?:((?:9\d|[2-9])\d{3}\-?\d{4}))$/', $number, $matches) === false):
        return null;
    endif;
    
    $ddi = $matches[1] ?? '';
    $ddd = preg_replace('/^0/', '', $matches[2] ?? '');
    $phone = $matches[3] ?? '';
    
    if(!empty($ddi)):
        return $ddi.$ddd.$phone;
        
    elseif(!empty($ddd)):
        return substr($tddd,0,2).$ddd.$phone;

    elseif(!empty($number)):
        return $tddd.$phone;

    else:
        return $number;
    endif;
    
}

