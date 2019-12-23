<?php

use Illuminate\Database\Seeder;
use App\Models\Prefix;

class DdiPrefixesTableSeeder extends Seeder
{
    public function run()
    {
        $ddi = fopen("database/seeds/CodesDDI.txt", "r");
                $i=0;
                while ($arrayLine = fgetcsv($ddi, 1000, ";")):
                    //dd($arrayLine);
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
                            'service'     => "INTERNACIONAL", 
                        ]
                    );
                    $i++;
                    echo "$prefix => linha $i\n";
                    unset($prefix,$locale);            
                endwhile;
    }
}
