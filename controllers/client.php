<?php

namespace controllers {
    /*
	Classe Client
	*/
    class Client
    {
        /*
		getStatus()
		Verifica os status dos serviços da total voice
		*/
        public function getStatus()
        {
            global $app;
            $app->response()->header('Content-Type', 'application/json;charset=utf-8');

            //Pegando Endereço IP do Host
            $address = gethostbyname('api.totalvoice.com.br');

            //Abrindo Conexão
            $socket = fsockopen($address, 80, $errno, $errstr, 30);
            $result = '';
            if (!$socket) {
                echo "$errstr ($errno)<br />\n";
            } else {
                //Caso não tenha erro, escreve e envia o Cabeçalho 
                $out = "GET /status HTTP/1.1\r\n";
                $out .= "Content-Type: application/json\r\n";
                $out .= "Host: api.totalvoice.com.br\r\n";
                $out .= "Access-Token: b484beb28b15f70e7544607e03aedb65\r\n"; // Token gerado pelo site da Total Voice
                $out .= "Connection: Close\r\n\r\n";

                fwrite($socket, $out);
                while (!feof($socket)) {
                    $result .= fread($socket, 128);
                }
                fclose($socket);
                //Tranformando em Array o cabeçalho enviado pelo Apache
                $response = explode("\r\n", $result);
                //Envia apenas o JSON do status
                $app->render('default.php', ["data" => $response[9]], 200);
            }
        }
        /*
		validaNumero()
		
		*/
        public function validaNumero()
        {
            global $app;
            $app->response()->header('Content-Type', 'application/json;charset=utf-8');
            // Recebe apenas o numero de telefone digitado 
            $post_data = explode("=", $app->request->getBody());
            // Transforma em padrão JSON para enviar
            $jsonData = "{  \"numero_destino\" : \"$post_data[1]\"  }";
            //Pegando Endereço IP do Host
            $address = gethostbyname('api.totalvoice.com.br');

            //Abrindo Conexão
            $socket = fsockopen($address, 80, $errno, $errstr, 30);
            $result = '';
            if (!$socket) {
                echo "$errstr ($errno)<br />\n";
            } else {
                //Caso não tenha erro, escreve e envia o Cabeçalho 
                $out = "POST /valida_numero HTTP/1.1\r\n";
                $out .= "Content-Type: application/json\r\n";
                $out .= "Accept: application/json\r\n";
                $out .= "Host: api.totalvoice.com.br\r\n";
                $out .= "Content-length: " . strlen($jsonData) . "\r\n";
                $out .= "Access-Token: b484beb28b15f70e7544607e03aedb65\r\n"; // Token gerado pelo site da Total Voice
                $out .= "Connection: Close\r\n\r\n";
                $out .=  $jsonData . "\r\n";

                fwrite($socket, $out);
                while (!feof($socket)) {
                    $result .= fread($socket, 128);
                }
                fclose($socket);
                //Tranformando em Array o cabeçalho enviado pelo Apache
                $response = explode("\r\n", $result);
                //Envia apenas o JSON retornado
                $app->render('default.php', ["data" => $response[9]], 200);
            }
        }
    }
}
