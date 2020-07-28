<?php

function panasonic_tda_tde_ns($file,$name){

    $cdrs = Storage::disk('local')->get($file);
    $cdrs = preg_split('/(\r|\n)/', $cdrs);
    
    foreach($cdrs as $cdr):
        //Limpa as variaveis
        $pbx='' ; $calldate=''; $extensions_id=''; $trunks_id=''; $did=''; $direction=''; $dialnumber=''; $callnumber='';
        $ring=''; $billsec=''; $accountcodes_id=''; $projectcodes_id=''; $disposition=''; $status_id=''; $lg=0; 
        
        // Se a Linha começar com algo diferente de numero continua
        if(!is_numeric(substr($cdr,0,2)) ): continue; endif;

        $pbx = $name;                                                                                       //pabx do bilhete a ser importado
        //date and time
        list($d,$m,$y)   = explode('/',trim(substr($cdr,0,8)));                                             // data do bilhete
        $hi              = trim(substr($cdr,9,7));                                                          // hora e minuto do bilhete
        $calldate        = "20$y-$m-$d $hi:00";                                                             // data e hora da chamada convertida
        $extensions_id   = trim(substr($cdr,17,5));                                                         // ramal do bilhete
        $trunks_id       = trim(substr($cdr,23,4));                                                         // tronco do bilhete
        $ring            = str_replace("'", ":", trim(substr($cdr,79,4)));                                  // tempo de ring do bilhete
        $billsec         = str_replace("'", ":", trim(substr($cdr,84,8)));                                  // duração da chamada no bilhete
        $accountcodes_id = trim(substr($cdr,105,10));                                                       // codigo de conta do bilhete
        $projectcodes_id = trim(substr($cdr,105,10));                                                       // codigo de projeto do bilhete
        $disposition     = trim(substr($cdr,116,3));                                                        // flags do bilhete
        $ring            = strtotime($ring?'00:0'.$ring:'00:00:00') - strtotime('00:00:00');                // tempo de ring convertido
        $billsec         = strtotime($billsec?$billsec:'00:00:00') - strtotime('00:00:00');                 // duração da chamada convertido
        $status_id       = 0;                                                                               // status 0 não tarifado
        $continue        = false;                                                                           // marca continue como falso
        //checa o tipo da chamada
        switch(trim(substr($cdr,28,3))):

            // chamadas internas
            case 'EXT':
                list($a, $dialnumber) = explode('EXT',trim(substr($cdr,28,50)));                            // numero discado no bilhete
                $direction = 'IN';                                                                          // insere a flag IN para chamadas internas
                break;
            
            //  informações de log
            case 'LOG':
                list($a, $dialnumber) = explode('LOG',trim(substr($cdr,28,50)));                            // mensagem de log no bilhete                            
                $lg=1;                                                                                      // insere o valor 1 informando ser uma mensagem de log
                break;
            
            // Chamadas de Entrada com o DDR
            case '<D>':
                list($a, $did, $dialnumber) = preg_split('/(<D>|<I>)/', trim(substr($cdr,28,50)));          // numero do DDR e Numero discado no bilhete
                $callnumber = dialIc($dialnumber, $trunks_id, $pbx);                                        // numero convertido no padrão e.164
                $direction = 'IC';                                                                          // insere a flag IC para chamadas de entrada
                break;
                
            // Chamadas de Entrada sem DDR
            case '<I>':
                list($a, $dialnumber) = preg_split('/(<D>|<I>)/', trim(substr($cdr,28,50)));                // numero discado no bilhete 
                $callnumber = dialIc($dialnumber, $trunks_id, $pbx);                                        // numero convertido no padrao e.164
                $direction = 'IC';                                                                          // insere a flag IC para chamadas de entrada
                break;
            
            // Chamadas de saida sem marcação no bilhete
            default :
                if(is_numeric(trim(substr($cdr,28,2)) ) ):                                                  // verifica se o numero discado começa com um numero
                    $dialnumber = trim(substr($cdr,28,50));                                                 // numero discado no bilhete
                    $callnumber = dialOc($dialnumber, $trunks_id, $pbx);                                    // numero convertido para o padrão e.164
                    $direction = 'OC';                                                                      // insere a flag OC para chamadas de saida
                else:
                    $continue = true;                                                                       // se não casar com numero discado marca continue como true
                endif;
            break;
        endswitch;

        // continua se não conseguiu identificar o tipo de chamada no bilhete
        if($continue):
            continue;
            $continue = false;
        endif;
    
        if($lg==1):
            $log = App\Models\Agent::updateOrCreate(
                ['pbx' => $pbx, 
                'logdate' => $calldate, 
                'extensions_id' => $extensions_id, 
                'status_id' => $dialnumber, 
                ],
                
                ['pbx' => $pbx, 
                'logdate' => $calldate, 
                'extensions_id' => $extensions_id, 
                'status_id' => $dialnumber, 
                ]
            );
        else:
            $call = App\Models\Call::updateOrCreate(
                ['pbx' => $pbx, 
                'calldate' => $calldate, 
                'extensions_id' => $extensions_id, 
                'trunks_id' => $trunks_id, 
                'did' => $did, 
                'direction' => $direction, 
                'dialnumber' => $dialnumber==''?'NI':$dialnumber, 
                'ring' => $ring, 
                'billsec' => $billsec, 
                'accountcodes_id' => $accountcodes_id, 
                'projectcodes_id' => $projectcodes_id, 
                'disposition' => $disposition, 
                ],
                
                ['pbx' => $pbx, 
                'calldate' => $calldate, 
                'extensions_id' => $extensions_id, 
                'trunks_id' => $trunks_id, 
                'did' => $did, 
                'direction' => $direction, 
                'dialnumber' => $dialnumber==''?'NI':$dialnumber, 
                'callnumber' => $dialnumber==''?'NI':$callnumber, 
                'ring' => $ring, 
                'billsec' => $billsec, 
                'accountcodes_id' => $accountcodes_id, 
                'projectcodes_id' => $projectcodes_id, 
                'disposition' => $disposition, 
                'status_id' => $status_id, 
                ]
            );
        endif;
        $lg=0;
        //dd($call);
    endforeach;
    return true;
}

        