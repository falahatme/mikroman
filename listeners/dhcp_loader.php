<?php
/**
 * Created by PhpStorm.
 * User: mohammad falahat
 * Date: 12/10/2014
 * Time: 01:05 PM
 */

include "../ssh.php";





    $cidri = array(
        '1' => '128.0.0.0',
        '2' => '192.0.0.0',
        '3' => '224.0.0.0',
        '4' => '240.0.0.0',
        '5' => '248.0.0.0',
        '6' => '252.0.0.0',
        '7' => '254.0.0.0',
        '8' => '255.0.0.0',
        '9' => '255.128.0.0',
        '10' => '255.192.0.0',
        '11' => '255.224.0.0',
        '12' => '255.240.0.0',
        '13' => '255.248.0.0',
        '14' => '255.252.0.0',
        '15' => '255.254.0.0',
        '16' => '255.255.0.0',
        '17' => '255.255.128.0',
        '18' => '255.255.192.0',
        '19' => '255.255.224.0',
        '20' => '255.255.240.0',
        '21' => '255.255.248.0',
        '22' => '255.255.252.0',
        '23' => '255.255.254.0',
        '24' => '255.255.255.0',
        '25' => '255.255.255.128',
        '26' => '255.255.255.192',
        '27' => '255.255.255.224',
        '28' => '255.255.255.240',
        '29' => '255.255.255.248',
        '30' => '255.255.255.252',
        '31' => '255.255.255.254',
        '32' => '255.255.255.255',
    );





function inTrim($string){
    while(strpos($string, '  ')!==false){
        $string = str_replace('  ', ' ', $string);
    }
    return $string;
    }










$ssh1 = new ssh('192.168.1.1', 'mikroman', 'mikroman');







// 01  IP ADDRESS PRINT in array $address_print
$result = $ssh1->exec("ip address print", true);
$result = explode("\n", $result);

array_shift($result);
array_shift($result);
$result = array_filter($result);

$address_print = array();
foreach($result as $row){
    $row = trim($row);
    if(strlen($row)<1)
        continue;
    if(strpos($row, ' D ')!==false or strpos($row, ' I ')!==false or strpos($row, ' X ')!==false)
        continue;
    $fields = explode(' ', inTrim($row));
    $address_print[$fields[1]]['number'] = $fields[0];
    $address_print[$fields[1]]['address'] = $fields[1];
    $address_print[$fields[1]]['network'] = $fields[2];
    $address_print[$fields[1]]['interface'] = $fields[3];
    }
//print_r($address_print);
//echo "<hr />";




// 02 DHCP SERVER in array $lans
$result = $ssh1->exec("ip dhcp-server print ", true);
$result = explode("\r\n", $result);
array_shift($result);
array_shift($result);
$result = array_filter($result);

$dhcp_server = array();
$lans = array();
foreach($result as $row){
    $row = trim($row);
    if(strlen($row)<1)
        continue;
    if(strpos($row, ' I ')!==false or strpos($row, ' X ')!==false)
        continue;
    $fields = explode(' ', inTrim($row));
    $lans[$fields[2]] = $fields[1];
    }
//print_r($lans);
//echo "<hr />";





// 03 DHCP SERVER Network in array $dhcp_network
$result = $ssh1->exec("ip dhcp-server network print", true);
$result = explode("\n", $result);

array_shift($result);
array_shift($result);
$result = array_filter($result);

$dhcp_network = array();
foreach($result as $row){
    $row = trim($row);
    if(strlen($row)<1)
        continue;
    $fields = explode(' ', inTrim($row));
    if(is_numeric($fields[0])){
        $subnet = explode('/', $fields[1]);
        $full = $fields[2].'/'.$subnet[1];
        $dhcp_network[$full]['address'] = $fields[1];
        $dhcp_network[$full]['gateway'] = $fields[2];
        $dhcp_network[$full]['dns_server'] = $fields[3];
        $dhcp_network[$full]['subnet'] = $subnet[1];
        $dhcp_network[$full]['subnet_mask'] = $cidri[$subnet[1]];
    }
    }
//print_r($dhcp_network);
//echo "<hr />";



// 04 Check DHCP Existance and Detect Name Of Network
$dhcp_services = array();
foreach($dhcp_network as $address=>$row){
    if(strlen($lans[$address_print[$address]['interface']])>0)
        $dhcp_services[$lans[$address_print[$address]['interface']]] = array('address' => $address_print[$address], 'network' => $row);
}

echo "<pre>" . print_r($dhcp_services, 1) . "</pre><hr /><hr />";




if(count($dhcp_services)==0){
    
    die('<h1>Oops! There are no DHCP servers.</h1>');
    
    }







/*


$result = $ssh1->exec("ip dhcp-server lease print detail", true);
$result = explode("\r\n", $result);
array_shift($result);
$result = implode("\r\n", $result);
$result = explode("\r\n\r\n", $result);
//echo "<pre>  ".print_r($result, 1)."  </pre>" . "<hr />";


foreach($result as $client){
    if(strlen(trim($client))==0)
        continue;
    $client = str_replace("\n", ' ', $client);
    
    $client_infos = explode(' ', inTrim($client));
    $clients_info = array();
    foreach($client_infos as $client_info){
        if(strlen(trim($client_info))==0)
            continue;
        $row = explode('=', $client_info);
        $clients_info[trim($row[0])] = trim($row[1]);
    }
echo "<pre>".print_r($clients_info, 1)."</pre><hr />";
    $dhcp_record = array(
    'type' => 'assign',
    'host_name' => $clients_info[''];
    );
}
*/

echo "<pre>".print_r($dhcp_services, 1)."</pre>";

