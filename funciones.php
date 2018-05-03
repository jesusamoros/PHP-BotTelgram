<?php

// convert kelvin to cent
function kel2cent ($kelvin){
    $cent = $kelvin - 273.15;
    return $cent;

}


function tiempo(){
//http://api.openweathermap.org/data/2.5/weather?q=ELCHE,ES&appid=bb7d1605ff04a95c9eac0d718e0632ba
	$cho  = curl_init();
	curl_setopt($cho, CURLOPT_URL,"http://api.openweathermap.org/data/2.5/weather?q=ELCHE,ES&appid=VUESTRAAPIKEY");
	curl_setopt($cho, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec ($cho); 
	curl_close ($cho);
    $resp =json_decode($output,true);
    
    $lugar         =  $resp['name'];
    $tiempo        =  $resp['weather'][0]['main'];
    $tiempo_desc   =  $resp['weather'][0]['description'];
    $temp          =  kel2cent($resp['main']['temp']);
    $temp_max      =  kel2cent($resp['main']['temp_max']);
	$temp_min      =  kel2cent($resp['main']['temp_min']);
	
    $r=  " hoy el dia en $lugar esta   $tiempo $tiempo_desc y tenemos una temperatura de $temp º , la maxima prevista para hoy sera de $temp_max º y la minima de $temp_min º";  
    return $r;
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
     $url="https://api.telegram.org/botXXXXXX:APIKEY/getUpdates";
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
