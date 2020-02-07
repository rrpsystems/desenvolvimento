<?php
//Arquivo de Conexões


//Conexão Telnet
function telnet($name, $host, $port, $user, $password)
{
    $control =0;
    $receve =1;
    $filename = date('d-m-Y_H-i-s').'.cdr';
    wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Coleta Telnet Inicio', $name);
 
    //tempo maximo de execução em Segundos
    set_time_limit(90);
 
    //desativa saida de erros
    error_reporting(0);
 
    // Abre um socket e passa o host e a porta como parametros
    $conn = stream_socket_client(trim($host).':'.trim($port), $errno, $errstr, 30);
	stream_set_blocking( $conn , FALSE );
    
    if (!$conn):
        wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> '.$errstr.' => '. $errno, $name);
        exit();        
    else:
        // Se a conexão foi bem sucedida manda o usuario e a senha
        sleep(3);
        fwrite($conn, trim($user)."\r\n");
        sleep(3);
        fwrite($conn, trim($password)."\r\n");
        sleep(3);
        // Cria um cabecalho no arquivo para facilitar a identificação de falhas
        wr_file($name, "\r\n"                                                    , $filename);
        wr_file($name, "--------------------------------------------------------", $filename);
        wr_file($name, "Conexao Telnet ".$name." - ".date('d-m-Y \a\t H:i:s')    , $filename);
        wr_file($name, "--------------------------------------------------------", $filename);
        wr_file($name, "\r\n"                                                    , $filename);
        
        // Pega as Linhas da conexão e escreve no arquivo de texto
        while (!feof($conn) ):
            $linha = fgets($conn);
            
            if($linha != ""):
                wr_file($name, $linha, $filename);
                $receve ++;
                $control =0;
          
            else:
                $control ++;
                //controle para equipamentos que nao enviam o feof de desconexão
                if($control == 200000):
                    break;
          
                endif;
            endif;
		endwhile;
    endif;

    fclose($conn);
    wr_log(date('d-m-Y_H-i-s')." -> $name, Recebido -> $receve linhas, Salvo no Arquivo -> $filename", $name);
                
    wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Coleta Telnet Termino', $name);
    unset($conn,$linha);
}


function tcp($name, $host, $port, $user, $password)
{
}

function ftp($name, $host, $port, $user, $password)
{
    $filename = date('d-m-Y_H-i-s').'.cdr';
    wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Coleta FTP Inicio', $name);
 
    //tempo maximo de execução em Segundos
    set_time_limit(90);
 
    //desativa saida de erros
    error_reporting(0);
 
    // Abre a conexão com o servidor FTP
    $ftp = ftp_connect($host, $port);
    if(!$ftp):
        wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Não Foi Possivel Estabelecer Conexão com '.$host , $name);
        die();
    endif;
    
    // Faz o login no servidor FTP
    $login = ftp_login($ftp, $user, $password);
    if(!$login):
        wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Login ou Senha Incorreto '.$host );
        die();
    endif;
    
    // Altera o diretório atual para "/"
    //ftp_chdir($ftp, "/");
    
    // Lista os arquivos na forma de array() ok;
    $files = ftp_nlist($ftp, ".");
    if(!$files):
        wr_log(date('d-m-Y_H-i-s')." -> $name -> nao há arquivos a serem coletdor no FTP", $name);
    endif;
    //cria o diretorio ok;
    Storage::makeDirectory('bilhetes/'.$name.'/'); // Pasta (externa)
    
    //pega a path do diretorio criado ok;
    $folder = Storage::disk('local')->path('bilhetes/'.$name.'/');
    
    foreach($files as $file):
        // Define variáveis para o recebimento de arquivo
        $remoteFile = $file; // Localização arquivo ftp
        $localFile = $folder.$filename; // localização e nome do arquivo local
        $size = ftp_size($ftp, $file); // tamanho do arquivo recebido
       
        // Recebe o arquivo pelo FTP em modo ASCII
        $receve = ftp_get($ftp, $localFile , $remoteFile, FTP_ASCII); // Retorno: true / false
        
        if($receve):
            wr_log(date('d-m-Y_H-i-s')." -> $name, Recebido -> $remoteFile, Salvo -> $filename, tamanho -> $size bytes ", $name);
            $delete = ftp_delete($ftp, $remoteFile);
        
            if (!$delete):
                wr_log(date('d-m-Y_H-i-s')." -> $name -> Nao foi possivel Excluir o -> $remoteFile -> tamanho -> $size do servidor FTP", $name);
        
            endif;
        endif;
    endforeach;

    // Encerra a conexão ftp
    ftp_close($ftp);

    wr_log(date('d-m-Y_H-i-s').' -> '.$name.' -> Coleta FTP Termino', $name);

}

function arquivo($name)
{
}
