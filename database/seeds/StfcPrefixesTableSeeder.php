<?php

use Illuminate\Database\Seeder;
use App\Models\Prefix;

class StfcPrefixesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stfc = fopen("database/seeds/CodesSTFC.txt", "r");
                $i=0;
                while ($arrayLine = fgetcsv($stfc, 1000, ";")):
                    
                    if(!isset($arrayLine[3])):
                        continue;
                    endif;
                    
                    list($carrier,$cnpj,$areacode,$prefix,$fi,$ff,$cc,$locale,$city,$cl,$st) = $arrayLine;
                    
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
                            'locale'      => $locale, 
                            'service'     => "FIXO", 
                        ]
                    );
                    $i++;
                    echo "55$areacode$prefix => linha $i\n";
                    unset($carrier,$cnpj,$areacode,$prefix,$fi,$ff,$cc,$locale,$city,$cl,$st);            
                endwhile;
           
    }
}
