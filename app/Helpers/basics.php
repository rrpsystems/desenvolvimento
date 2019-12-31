<?php

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

    function directions(){
        $directions = [
            'IC' => 'IC',
            'OC' => 'OC',
            'IN' => 'IN',
            'TL' => 'TL',
        ];

        return json_decode(json_encode($directions));
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