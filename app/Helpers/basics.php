<?php

    function secHours($sec){

        $is = gmdate("i:s", ($sec));    
        $H = (gmdate("d", ($sec))-1)*24 + gmdate("H", ($sec));
        return "$H:$is";
    }
    
    function services(){
        $services = [
            'STFC'     => 'STFC',
            'SMP'      => 'SMP',
            'DDI'      => 'DDI',
            'GRATUITO' => 'GRATUITO',
            'SERVIÇO'  => 'SERVIÇO',
            'OUTROS'   => 'OUTROS',
        ];

        return json_decode(json_encode($services));

    }

    function types(){
        $types = [
            'LDI'      => 'LDI',
            'LOCAL'    => 'LOCAL',
            'LDN'      => 'LDN',
            'VC1'      => 'VC1',
            'VC2'      => 'VC2',
            'VC3'      => 'VC3',
            'SERVIÇOS' => 'SERVIÇOS',
            'OUTROS'   => 'OUTROS',
            'GRATUITO' => 'GRATUITO',
            'TIE_LINE' => 'TIE_LINE',
        ];
    
        return json_decode(json_encode($types));
    
    }

    function rtype($t){
        $types = [
            'INT'      => 'INTERNO',
            'LDI'      => 'DDI',
            'LOCAL'    => 'FIXO LOCAL',
            'LDN'      => 'FIXO DDD',
            'VC1'      => 'MOVEL LOCAL',
            'VC2'      => 'MOVEL DDD',
            'VC3'      => 'MOVEL DDD',
            'SERVIÇOS' => 'SERVIÇOS',
            'OUTROS'   => 'OUTROS',
            'GRATUITO' => 'GRATUITO',
            'TIE_LINE' => 'TIE_LINE',
        ];

        if(array_key_exists($t, $types)):
            return $types[$t];
        else:
            return $t;
        endif;
    
    }
    
    function status($s){
        $status = [
            ''   => 'Á Tarifar',
            '0'  => 'Á Tarifar',
            '1'  => 'Tarifada',
            '91' => 'Prefxo Não Cadastrado',
            '92' => 'Tronco Não Cadastrado',
            '93' => 'Verificar 93',
            '94' => 'Verificar 94',
            '95' => 'Verificar 95',
            '96' => 'Verificar 96',
            '97' => 'Verificar 97',
            '98' => 'Verificar 98',
            '99' => 'Verificar 99',
        ];

        if(array_key_exists($s, $status)):
            return $status[$s];
        else:
            return $s;
        endif;
    
    }

    function directions(){
        $directions = [
            'IC' => 'IC',
            'OC' => 'OC',
            'IN' => 'IN',
            'TL' => 'TL',
        ];

        return json_decode(json_encode($directions));
    }

    function rdirection($d){
        $directions = [
            'IC' => 'ENTRADA',
            'OC' => 'SAIDA',
            'IN' => 'INTERNO',
            'TL' => 'TIE-LINE',
        ];
        if(array_key_exists($d, $directions)):
            return $directions[$d];
        else:
            return $d;
        endif;
    }

        function ucname($string) {
            $string =ucwords(strtolower($string));

            return $string;
    }

    function models(){
        $models = [
            'Panasonic_TDA_TDE_NS' => 'Panasonic_TDA_TDE_NS',
            'Intelbras_Impacta'    => 'Intelbras_Impacta',
            'Siemens_HP3000'       => 'Siemens_HP3000',

        ];
        
        return json_decode(json_encode($models));
    }
    
    function connections(){ 
        $connections = [
            'Arquivo' => 'Arquivo',
            'FTP'     => 'FTP',
            'TCP'     => 'TCP',
            'Telnet'  => 'Telnet',
        ];
        
        return json_decode(json_encode($connections));
    }

    function dials(){
         $dials = [
            'Pais + DDD + Telefone'             => 'Pais + DDD + Telefone',
            '0 + Operadora + DDD + Telefone'    => '0 + Operadora + DDD + Telefone',
            '0 + DDD + Telefone'                => '0 + DDD + Telefone',
            'DDD + Telefone'                    => 'DDD + Telefone',
        ];
        
        return json_decode(json_encode($dials));

    }