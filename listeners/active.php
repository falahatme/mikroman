<?php

// database
require_once "db.php";
require_once "browser.php";

// define no limit for timeout execution.
set_time_limit(0);
@date_default_timezone_set('ASIA/TEHRAN');
$browser = new Browser();


$hostip = @gethostbynamel('192.168.1.3');

do{
    sleep(1);
    $socket = stream_socket_server("udp://{$hostip[0]}:1113", $errno, $errstr, STREAM_SERVER_BIND);
}while(!$socket);


// Define some variables for grouping the rows;
$row = array();
$dhcp = array();
$firewall = array();


$firewallTimeout = 60;


// start listening to socket stream.
do {

    usleep(100);
    $string = stream_socket_recvfrom($socket, 384, 0, $peer);


    list($type, $string) = explode(',', $string, 2);

	//echo $string . "<br />";

    switch($type){

        case 'web-proxy':

            //if(strpos($string, 'Date')!==false)
            list($row['date'], $row['time']) = array(date('Y/m/d'), date('H:i:s'));

            if(strpos($string, "account 192")!==false){

                if(!empty($row)) {
                    //echo "<hr />";
		    //echo "<pre>".print_r($row, 1)."</pre>";
                    unset($row['debug_via']);
		    table('log')->fields($row)->insert();
                    //echo "<pre style='color:gray; font-family: calibri; border:1px solid silver; font-size: 9pt;'>". print_r($row, 1) . nl2br($strings) . "</pre>";
                }


                $string = str_replace('account ', '', $string);
                if(strpos($string, ' GET ')!==false){
                    str_replace('  ', ' ', $string);
                    $temp = explode(' ', $string);
                    unset($row);
                    $row = array(
                        'ip' => $temp[0],
                        'request' => $temp[2],
                        'action' => @ array_shift(explode(' ', array_pop(explode('action=', $string))))
                    );
                }

            }
            else{

                $string = str_replace('debug ', '', $string);

                if(strpos($string, 'User-Agent')!==false){
                    $browser->setUserAgent(str_replace('User-Agent: ', '', trim($string)));
                    list($row['browser'], $row['platform']) = array($browser->getBrowser().' '.$browser->getVersion(), $browser->getPlatform());

                }

                if(strpos($string, 'Via')!==false) {
                    $row['debug_via'] = str_replace('Via: ', '', $string);
                }

                if(strpos($string, 'Content-Type')!==false) {
                    $row['content_type'] = str_replace('Content-Type: ', '', $string);
                }

                if(strpos($string, 'Host')!==false) {
                    $row['host'] = str_replace('Host: ', '', $string);
                }

                if(strpos($string, 'Content-Length')!==false) {
                    $row['content_length'] = str_replace('Content-Length: ', '', $string);
                }

                if(strpos($string, 'Referer')!==false) {
                    $row['referer'] = str_replace('Referer: ', '', $string);
                }


            }

            break;

        case 'firewall':

            $array = explode('forward: ', $string);
            $string = $array[1];

            if(strpos($string, '(ACK)')!==false){

                $array = explode(',', $string);
                $ack = explode('->', $array[3]);
                $full = explode('->', $array[4]);

                $mac = trim(str_replace('src-mac', '', $array[1]));
                $client = $ack[0];
                $target = $ack[1];
                $public = str_replace(')', '', $full[1]);

                if(!isset($firewall["$target"])) {

                    $firewall["$target"]['start'] = time();
                    $firewall["$target"]['end'] = time();
                    $firewall["$target"]['mac'] = trim($mac);

                    $splitted_client = explode(':', $client);
                    $splitted_target = explode(':', $target);
                    $splitted_public = explode(':', $public);

                    $firewallTbl = array(
                        'start' => date('Y-m-d H:i:s', $firewall["$target"]['start']),
                        'end' => date('Y-m-d H:i:s', $firewall["$target"]['end']),
                        'mac' => trim($mac),
                        'ip' => trim($splitted_client[0]),
                        'port' => trim($splitted_client[1]),
                        'target_ip' => trim($splitted_target[0]),
                        'target_port' => trim($splitted_target[1]),
                        'public_ip' => trim($splitted_public[0])
                    );

                    $firewall["$target"]['insert_id'] = table('firewall')->fields($firewallTbl)->insert();
                    //echo '<hr />';
                }else{

                    if(time() <= ($firewall["$target"]['end']+$firewallTimeout)){

                        $firewall["$target"]['end'] = time();
                        table('firewall')->fields(array('end' => date('Y-m-d H:i:s', time())))->where(array("firewall_id=".$firewall["$target"]['insert_id']))->update();
                        //echo '<hr />';

                    }else{

                        unset($firewall["$target"]);

                        $firewall["$target"]['start'] = time();
                        $firewall["$target"]['end'] = time();
                        $firewall["$target"]['mac'] = trim($mac);

                        $splitted_client = explode(':', $client);
                        $splitted_target = explode(':', $target);
                        $splitted_public = explode(':', $public);

                        $firewallTbl = array(
                            'start' => date('Y-m-d H:i:s', $firewall["$target"]['start']),
                            'end' => date('Y-m-d H:i:s', $firewall["$target"]['end']),
                            'mac' => trim($mac),
                            'ip' => trim($splitted_client[0]),
                            'port' => trim($splitted_client[1]),
                            'target_ip' => trim($splitted_target[0]),
                            'target_port' => trim($splitted_target[1]),
                            'public_ip' => trim($splitted_public[0])
                        );

                        $firewall["$target"]['insert_id'] = table('firewall')->fields($firewallTbl)->insert();
                        //echo '<hr />';


                    }

                }


            }


            break;

        case 'dhcp':

            $eoq = false;



            if(strpos($string, 'deassigned')!==false) {

                unset($dhcp);
                $dhcp = array();
                $typeInfo = array_reverse(explode(' ', $string));
                $dhcp['mac'] = $typeInfo[0];
                $dhcp['client_id'] = $typeInfo[2];
                $dhcp['type'] = 'deassign';
                $eoq = true;


            }elseif(strpos($string, 'assigned')!==false) {

                $typeInfo = array_reverse(explode(' ', $string));
                $dhcp['mac'] = $typeInfo[0];
                $dhcp['client_id'] = $typeInfo[2];
                $dhcp['type'] = 'assign';

            }


            if(strpos($string, 'Host-Name ')!==false) {
                $dhcp['host_name'] = trim(str_replace(array('debug,packet ', 'Host-Name = ', '"'), '', $string));
            }

            if(strpos($string, 'Class-Id ')!==false) {
                $dhcp['class_id'] = trim(str_replace(array('debug,packet ', 'Class-Id = ', '"'), '', $string));
            }

            if(strpos($string, 'Server-Id ')!==false) {
                $dhcp['server_id'] = trim(str_replace(array('debug,packet ', 'Server-Id = ', '"'), '', $string));
            }

            if(strpos($string, 'Address-Time ')!==false) {
                $dhcp['address_time'] = trim(str_replace(array('debug,packet ', 'Address-Time = ', '"'), '', $string));
            }

            if(strpos($string, 'Subnet-Mask ')!==false) {
                $dhcp['subnet_mask'] = trim(str_replace(array('debug,packet ', 'Subnet-Mask = ', '"'), '', $string));
                $eoq = true;
            }



            if(@$eoq){
                if(isset($dhcp['type']))
                    table('dhcp_server')->fields($dhcp)->insert();
                    //echo "<pre style='color:gray; font-family: calibri; border:1px solid silver; font-size: 9pt;'>". print_r($dhcp, 1) . "</pre>";
                unset($dhcp);
                $dhcp = array();

            }

            //echo "<pre style='color:gray; font-family: calibri; border:1px solid silver; font-size: 9pt;'>". nl2br($string) . "</pre>";
        break;

    }




    stream_socket_sendto($socket, date("D M j H:i:s Y\r\n"), 0, $peer);

    Database::$db->query('UPDATE server_settings SET value=NOW() WHERE setting="last_listen_time"');
    //table('server_status')->fields(array('last_listen_time' => 'now()'))->update(true);

} while (1);

socket_close($socket);

?>
