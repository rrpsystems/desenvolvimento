<?php
//Arquivo de Conexões


//Conexão Telnet
function telnet($name, $host, $port, $user, $password)
{
    $filename = date('d-m-Y_H-i-s').'.cdr';
    wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Coleta Telnet Inicio', $name);
 
    //tempo maximo de execução em Segundos
    set_time_limit(180);
 
    //desativa saida de erros
    error_reporting(0);
 
    // Abre um socket e passa o host e a porta como parametros
    $conn = stream_socket_client(trim($host).':'.trim($port), $errno, $errstr, 30);
	stream_set_blocking( $conn , FALSE );
    $end = " ''\r\n ''\r\n ''\r\n ''\r\n ''\r\n ''\r\n ''\r\n ''\r\n ''\r\n ''\r\n";

    if (!$conn):
        wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> '.$errstr.' => '. $errno, $name);
        
    else:
        // Se a conexão foi bem sucedida manda o usuario e a senha
        fwrite($conn, trim($user)."\r\n");
        fwrite($conn, trim($password)."\r\n");
        // Cria um cabecalho no arquivo para facilitar a identificação de falhas
        wr_file($name, "\r\n"                                                    , $filename);
        wr_file($name, "--------------------------------------------------------", $filename);
        wr_file($name, "Conexao Telnet ".$name." - ".date('d-m-Y \a\t H:i:s')    , $filename);
        wr_file($name, "--------------------------------------------------------", $filename);
        wr_file($name, "\r\n"                                                    , $filename);
        
        // Pega as Linhas da conexão e escreve no arquivo de texto
        while (!feof($conn) || $conn == $end):
            $linha = fgets($conn);
            
            if($linha != ""):
                wr_file($name, $linha, $filename);
            
            endif;
		endwhile;
    endif;

    fclose($conn);    
    wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Coleta Telnet Termino', $name);
}


function tcp($name, $host, $port, $user, $password)
{
}

function ftp($name, $host, $port, $user, $password)
{
}

function arquivo($name)
{
}