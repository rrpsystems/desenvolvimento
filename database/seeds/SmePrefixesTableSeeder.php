<?php

use Illuminate\Database\Seeder;
use App\Models\Prefix;

class SmePrefixesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sme = fopen(base_path().'/database/seeds/CodesSME.txt', "r");
        
        while ($arrayLine = fgetcsv($sme, 1000, ";")):
            
            if(empty(implode($arrayLine))):
                continue;
            endif;
            
            list($carrier, $cnpj, $areacode, $prefix, $fi, $ff, $st) = $arrayLine;
            
            Prefix::updateOrCreate(
                [
                    'prefix' => "55$areacode$prefix", 
                ],
                [
                    'carrier'     => $carrier, 
                    'areacode'    => $areacode, 
                    'country'     => "BRASIL", 
                    'dadd'        => strlen(trim($ff)), 
                    'countrycode' => "55", 
                    'locale'      => "CELULAR DDD $areacode", 
                    'service'     => "SMP", 
                ]
            );

            unset($carrier, $cnpj, $areacode, $prefix, $fi, $ff, $st);            
        endwhile;
   }
}
