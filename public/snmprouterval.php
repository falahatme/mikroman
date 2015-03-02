<?php

include "../listeners/db.php";
include "config/functions.php";

$router_id = intval($_GET['router_id']);
@$interface_id = intval($_GET['interface_id']);
$type = Database::$db->real_escape_string($_GET['type']);


$router = Database::$db->query("SELECT router_ip, trap_community FROM router_snmp_detail LEFT JOIN routers ON (routers.routers_id=router_snmp_detail.router_id) WHERE router_id=$router_id")->fetch_object();


if($type=="cpu"){
    
    $cpuUsage = snmprealwalk($router->router_ip, $router->trap_community, '1.3.6.1.2.1.25.3.3.1.2.1');
    $sum = 0;
    foreach($cpuUsage as $usage){
		$sum += intval(trim(str_replace('INTEGER:', '', $usage)));
    }
    echo $sum;
    die();

}elseif($type=="memory"){
    
    $memUsage = snmprealwalk($router->router_ip, $router->trap_community, '1.3.6.1.2.1.25.2.3.1.6');
    $sum = 0;
    foreach($memUsage as $usage){
		$sum += intval(trim(str_replace('INTEGER:', '', $usage)));
	    echo $sum;
	    die();
    }

}elseif($type=="uptime"){
    
    $memUsage = snmprealwalk($router->router_ip, $router->trap_community, '1.3.6.1.2.1.1.3.0');
    foreach($memUsage as $usage){
		$uptime = str_replace('.00', '', array_pop(explode(' ', trim(str_replace('Timeticks:', '', $usage)))));
	    echo $uptime;
	    die();
    }

}elseif($type=="interfaceStatus"){

	$snmpastatus = snmprealwalk($router->router_ip, $router->trap_community, '1.3.6.1.2.1.2.2.1.7.'.$interface_id);
	$snmpostatus = snmprealwalk($router->router_ip, $router->trap_community, '1.3.6.1.2.1.2.2.1.8.'.$interface_id);
	$status['a'] = trim(str_replace("INTEGER:", '', array_pop($snmpastatus)));
	$status['o'] = trim(str_replace("INTEGER:", '', array_pop($snmpostatus)));

    if($status['a'] == 2){
        echo 0;
    }
    elseif($status['o'] == 2){
        echo 1;
    }elseif($status['o'] == 1){
        echo 2;
    }
    die();

}



?>
