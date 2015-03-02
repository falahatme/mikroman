<?php

//Reduce errors
error_reporting(~E_WARNING);
set_time_limit(0);

//Create a UDP socket
if(!($sock = socket_create(AF_INET, SOCK_RAW, 0)))
{
$errorcode = socket_last_error();
$errormsg = socket_strerror($errorcode);

die("Couldn't create socket: [$errorcode] $errormsg \n");
}

echo "Socket created \n";

// Bind the source address
if( !socket_bind($sock, "0.0.0.0" , 514) )
{
$errorcode = socket_last_error();
$errormsg = socket_strerror($errorcode);

die("Could not bind socket : [$errorcode] $errormsg \n");
}

echo "Socket bind OK \n";



$strings = "";
//$row = array();

//Do some communication, this loop can handle multiple clients
while(1)
{

    ob_flush();
    flush();
    ob_flush();
    $buf = "";

    //echo "Waiting for data ... \n";

    //Receive some data
    $r = socket_recvfrom($sock, $buf, 655120, 0, $remote_ip, $remote_port);

    // Give Start And End of a query
    $string = $buf;
    $string = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string);
    $string = str_replace('ExpOK', '', $string);

    if(strpos($string, "account 192")){

        //echo nl2br($string);
        //$all[] = $strings;

        if(strlen($strings)>0)
            echo "<p style='color:gray; font-family: calibri; border:1px solid silver; font-size: 9pt;'>$remote_ip : $remote_port -- " . nl2br(htmlentities($strings)) ."</p>";

        $strings = $string."\r\n";
        //$row = array('account' => $string);

   }else{

        $strings = $strings.$string."\r\n";
        //if(strpos($string, ''))
            //$row[''] = $string;

    }


    //Send back the data to the client
    socket_sendto($sock, "OK " . $string , 100 , 0 , $remote_ip , $remote_port);

    ob_flush();
    flush();
    ob_flush();


}

socket_close($sock);