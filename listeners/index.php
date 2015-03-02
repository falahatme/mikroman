<?php

header( 'Content-type: text/html; charset=utf-8' );

require_once("SocketServer.class.php"); // Include the Class File
$server = new SocketServer('192.168.1.1', 514); // Create a Server binding to the default IP address (null) and listen to port 31337 for connections
$server->max_clients = 10000; // Allow no more than 10 people to connect at a time
$server->hook("CONNECT","handle_connect"); // Run handle_connect everytime someone connects
$server->hook("INPUT","handle_input"); // Run handle_input whenever text is sent to the server
$server->infinite_loop(); // Run Server Code Until Process is terminated.

$count = 0;

/*
 * All hooked functions are sent the parameters $server (The server class), $client (the connection), and $input (anything sent, if anything was sent)
 * You should save the variables $server and $client using an ampersand (&) to make sure they are references to the objects and not duplications.
 */
function handle_connect(&$server,&$client,$input)
{
    SocketServer::socket_write_smart($client->socket,"String? ",""); // Outputs 'String? ' without a Line Ending
}
function handle_input(&$server,&$client,$input)
{
    $trim = trim($input); // Trim the input, Remove Line Endings and Extra Whitespace.


    // show input string with count:
    global $count;
    $count++;
    echo "<p style='color:gray; font-family: calibri; border:1px solid silver; font-size: 9pt;'>Output count $count: $output</p>";

    if(strtolower($trim) == "quit") // User Wants to quit the server
    {
        SocketServer::socket_write_smart($client->socket,"Oh... Goodbye..."); // Give the user a sad goodbye message, meany!
        $server->disconnect($client->server_clients_index); // Disconnect this client.
        return; // Ends the function
    }

    $output = strrev($trim); // Reverse the String

    SocketServer::socket_write_smart($client->socket,$output); // Send the Client back the String
    SocketServer::socket_write_smart($client->socket,"String? ",""); // Request Another String


}




