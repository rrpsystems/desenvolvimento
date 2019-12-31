<?php

function intelbras_impacta($file,$name){

        $cdrs = Storage::disk('local')->get($file);
        $cdrs = preg_split('/(\r|\n)/', $cdrs);
        
        //Usa o ano do bilhete de referencia para as ligações
        list($date, $time) = explode('_', basename($file));
        list($D, $M, $Y) = explode('-', $date);
       
        foreach($cdrs as $cdr):
                $pbx='' ; $calldate=''; $extensions_id=''; $trunks_id=''; $did=''; $direction=''; $dialnumber=''; $callnumber='';
                $ring=''; $billsec=''; $accountcodes_id=''; $projectcodes_id=''; $disposition=''; $status_id='';  

                if(!substr($cdr,0,2)):
                        continue;
                endif;
            
                $pbx = $name;
                //date and time
                list($d,$m)        = explode('/',trim(substr($cdr,53,5)));
                $hi                = trim(substr($cdr,59,5));
                $calldate          = "$Y-$m-$d $hi:00";
                $extensions_id     = trim(substr($cdr,0,10));
                $ring              = '0';
                $billsec           = trim(substr($cdr,65,6));
                $billsec           = $billsec?$billsec:'0';
                //$accountcodes_id = trim(substr($cdr,105,10));
                //$projectcodes_id = trim(substr($cdr,105,10));
                $disposition       = trim(substr($cdr,73,2));
                $status_id         = 0;
            
                switch(trim(substr($cdr,72,1))):
                
                        case 'I':
                                $dialnumber = trim(substr($cdr,11,10));
                                $direction  = 'IN'; 
                                break;
    
                        case 'E':
                                $trunks_id  = trim(substr($cdr,11,10));
                                $dialnumber = trim(substr($cdr,22,30));
                                $callnumber = dialIc($dialnumber, $trunks_id, $pbx);
                                $direction  = 'IC';
                                break;
                
                        case 'S':
                                $trunks_id  = trim(substr($cdr,11,10));
                                $dialnumber = trim(substr($cdr,22,30));
                                $callnumber = dialOc($dialnumber, $trunks_id, $pbx);
                                $direction  = 'OC';
                                break;
                            
                        default :
                endswitch;
                //se a data do bilhete for antes da data de coleta coloca o ano da coleta se nao um ano antes
                $time1 = str_replace('-', ':', substr($time,0,8));
                $now = strtotime($date.' '.$time1);
                $calldate = strtotime(date($calldate));
                if($now < $calldate):
                        $calldate =  date('Y-m-d H:i:s', strtotime('-1 year', $calldate));
                else:
                        $calldate =  date('Y-m-d H:i:s', strtotime('0 days',$calldate));
                endif;
        
                // insere no banco apenas ligações de entrada interna ou saida indentificadas no bilhete
                if($direction != ''):
                        $call = App\Models\Call::updateOrCreate(
                                ['pbx' => $pbx, 
                                'calldate' => $calldate, 
                                'extensions_id' => $extensions_id, 
                                'trunks_id' => $trunks_id, 
                                'did' => $did, 
                                'direction' => $direction, 
                                'dialnumber' => $dialnumber, 
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
                                'dialnumber' => $dialnumber, 
                                'callnumber' => $callnumber, 
                                'ring' => $ring, 
                                'billsec' => $billsec, 
                                'accountcodes_id' => $accountcodes_id, 
                                'projectcodes_id' => $projectcodes_id, 
                                'disposition' => $disposition, 
                                'status_id' => $status_id, 
                                ]
                                );
        
                        endif;
                //dd($call);
                endforeach;
                return true;
        }
    