<?php

use Illuminate\Database\Seeder;
use App\Models\Prefix;

class DdiPrefixesTableSeeder extends Seeder
{
    public function run()
    {
        $ddi = fopen(base_path().'/database/seeds/CodesDDI.txt', "r");
                $i=0;
                while ($arrayLine = fgetcsv($ddi, 1000, ";")):
                    
                    if(!isset($arrayLine[1])):
                        continue;
                    endif;
                    
                    list($prefix,$locale) = $arrayLine;
                    
                    Prefix::updateOrCreate(
                        [
                            'prefix' => "$prefix", 
                        ],
                        [
                            'country'     => $locale,
                            'dadd'        => '1', 
                            'countrycode' => $prefix, 
                            'locale'      => $locale, 
                            'service'     => "DDI", 
                        ]
                    );
                    
                    unset($prefix,$locale);            
                endwhile;
    }
}
