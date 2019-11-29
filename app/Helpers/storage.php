<?php

//armazena os arquivos coletados
function wr_file($folder, $line, $filename, $path = 'bilhetes')
{
    Storage::makeDirectory($path.'/'.$folder);
    Storage::disk('local')->append($path.'/'.$folder.'/'.$filename, $line);
}

//armazena os arquivos de log
function wr_log($line, $filename)
{
    $dir = 'log/'.date('Y/m/d').'/';
    Storage::makeDirectory($dir);
    Storage::disk('local')->append($dir.$filename, $line);
}

//le os arquivos
function re_file($file, $path = 'bilhetes')
{
    $dir = $path.'/'.$file;
    $allfiles = Storage::disk('local')->files($dir);
    foreach ($allfiles as $file):
        return $file;
    endforeach;
}
