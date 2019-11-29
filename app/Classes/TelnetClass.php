<?php

namespace App\Classes;

class TelnetClass{


    //Conexão Telnet 
    
    public function connect($name, $host, $port, $user, $password)
    {
        $log_name = date('d-m-Y_H-i-s');
        //tempo maximo de execução em Segundos
        set_time_limit (30);
        //desativa saida de erros
        error_reporting(0);
        // Abre um socket e passa o host e a porta como parametros
        $conn = stream_socket_client(trim($host).':'.trim($port), $errno, $errstr, 30);
    	stream_set_blocking ( $conn , FALSE );

        if (!$conn):
            echo "$errstr ($errno)<br />\n";
        
        else:
            // Se a conexão foi bem sucedida manda o usuario e a senha
            fwrite($conn, trim($user)."\r\n");
            fwrite($conn, trim($password)."\r\n");

            // Cria um cabecalho no arquivo para facilitar a identificação de falhas
            echo "--------------------------------------------------------";
            echo "\n";
            echo "Conexao Telnet ".$name." - ".date('d-m-Y \a\t H:i:s');
            echo "\n";
            echo "--------------------------------------------------------";
            echo "\n";

            // Pega as Linhas da conexão e escreve no arquivo de texto
            while (!feof($conn)):
                $linha = fgets($conn);

                if($linha != ""):
                    $this->wh_log($name, $linha, $log_name);

                endif;
                //Imprime as linhas na tela        
                echo $linha;
    			//die();
    		endwhile;
        endif;
    
        fclose($fp);
    }

    private function wh_log($dir_name, $log_msg, $log_name)
    {
    
        $log_filename = 'log/'.$dir_name.'/'.date('Y/m/d');
        if (!file_exists($log_filename)): 
            //cria o diretorio para salvar os arquivos.
            mkdir($log_filename, 0777, true);
        endif;

        $log_file_data = $log_filename.'/'.$log_name . '.log';
        file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
    }


    //$connections = $this->connection->orderBy('name')->get();
    //dd($connections);
}
