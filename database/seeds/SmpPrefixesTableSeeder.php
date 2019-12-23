<?php

use Illuminate\Database\Seeder;
use App\Models\Prefix;

class SmpPrefixesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i=0;
        $smp = fopen('database/seeds/CodesSMP.txt', "r");
        while ($arrayLine = fgetcsv($smp, 1000, ";")):
            
            if(!isset($arrayLine[3])):
                continue;
            endif;
            
            list($carrier, $cnpj, $areacode, $prefix, $fi, $ff, $st) = $arrayLine;
            
            Prefix::updateOrCreate(
                [
                    'prefix' => "55$areacode$prefix", 
                ],
                [
                    'carrier'       =>  $carrier, 
                    'areacode'      => $areacode, 
                    'country'       => "BRASIL",
                    'dadd'          => strlen(trim($ff)), 
                    'countrycode'   => "55", 
                    'locale'        => "CELULAR DDD $areacode", 
                    'service'       => "MOVEL", 
                ]
            );
            $i++;
            echo "55$areacode$prefix => linha $i\n";
            unset($carrier, $cnpj, $areacode, $prefix, $fi, $ff, $st);            
        endwhile;

    }
}
