<?php

//NOT COMPLETE , NOT WORK
function tiempo()
{
//Esta funcion es para obtener el tiempo en una ubicacion, servicio gratuito de openweathermap
    //http://api.openweathermap.org/data/2.5/weather?q=ELCHE,ES&appid=xxxxxxxxxxxxxxxxxxxxxxxxx
        $cho  = curl_init();
        $site="ELCHE,ES";
        $api="your_Api";
        curl_setopt($cho, CURLOPT_URL,"http://http://api.openweathermap.org/data/2.5/weather?q=$site&appid=$api");
        curl_setopt($cho, CURLOPT_POST, TRUE);
        curl_setopt($cho, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec ($cho); 
        curl_close ($cho);
        $resp =json_decode($output,true);
        var_dump($resp);
        // Pendiente de terminar la respuesta....
    
}
    
function sendMessage($text) 
{

    $chatId   = "you_chat_id";
    $TOKEN    = "your_token";
    $TELEGRAM = "https://api.telegram.org:443/bot$TOKEN"; 
    
    $query = http_build_query(array(
        'chat_id'=> $chatId,
        'text'=> $text,
        'parse_mode'=> "HTML", // Optional: Markdown | HTML
      ));
    
    $response = file_get_contents("$TELEGRAM/sendMessage?$query");
    //return the response of api telegram, nos devuelve  el resultado del envio del mensaje
    return $response;
}
    
    
function readSmS()
{ 
    
// Abre el fichero para obtener el contenido existente
    $fp = fopen('last_id.txt', 'r');
    $id_archivo = fgets($fp);
    //echo "Valor del fichero ".$id_archivo;
    fclose($fp);
     $url="https://api.telegram.org/bot204843205:AAEwfw0j0clyQOk41Z-NClx9D6n6CfVgXGM/getUpdates";
    $curl = curl_init("$url"); //PENDIENTE 
    curl_setopt($curl, CURLOPT_FAILONERROR, true); 
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   
    $resulto  = curl_exec($curl); 
    $response = json_decode($resulto, true);
    $Nmensajes = count($response["result"]);
    //print_r($response);
    
    // Recogemos los mensajes de uno o mas usuarios.
    for ($i = 0; $i < $Nmensajes; $i++) {
    
            //Comprobamos si hemos prodesado la peticion
            if ($response["result"][$i]["update_id"]  > $id_archivo   ){
    
                $id      =  $response["result"][$i]["update_id"];
                //Guardamos el último id de mensaje
                $smsid   =  $response["result"][$i]["message"]["message_id"];

                // Hay usuarios que tienen configurado el username si no lo tienen configurado obtenemos el first_name
                if (empty($response["result"][$i]["message"]["from"]["username"])){
                    $user    =  $response["result"][$i]["message"]["from"]["first_name"];
                }else{
                    $user    =  $response["result"][$i]["message"]["from"]["username"];
                }
                    
                $grupo   =  $response["result"][$i]["message"]["chat"]["title"];
                $comando =  $response["result"][$i]["message"]["text"];
                $datos_sms = array('id' => $id ,'smsid' => $smsid,'usuario' => $user,'grupo' => $grupo, 'comando' => $comando);
    
                $lista_opciones = "";//envio de datos o opciones
                $sendSmS = "Hola @".$user." en que te puedo ayudar?? , estos son los comandos $lista_opciones "; 
                sendMessage("$sendSmS");// llamamos a la funcion y contestamos automaticamente a cualquier comando, automatic reponse  when recibed comand /example
                $fp = fopen('last_id.txt', 'w');// Guardamos el último id al que hemos contestado, last id reply sms.
                fwrite($fp, "$id");
                fclose($fp);
    
             }
    
    
    
    }
    
        
        //sendMessage("$sendSmS");
    
}
    
    //////////////////////////////// FIN  FUNCIONES ////////////////////////////////////////////////////////////////////
    
   //PRUEBAS 
    //Iniciamos
    while(1)
    {
        readSmS();
        sleep(5); # un paro de 5 segundos antes de volver a iniciar las instrucciones
    }
    
    











?>
