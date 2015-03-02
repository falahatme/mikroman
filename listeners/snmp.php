<?php

error_reporting(E_ALL);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

set_time_limit(0);

require "db.php";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


function snmptime($snmpResult){
    
    $sectime = explode(' ', array_pop($snmpResult)); // test
    foreach($sectime as $part){
        if(strpos($part, ':')===false){
            $sectime = trim(str_replace(array('(', ')'), '', $part))/100;
            break;
        }
    }
    return $sectime;
    
}

$routers = Database::$db->query("SELECT routers_id, router_ip FROM routers RIGHT JOIN snmpinterfaces ON (routers.routers_id=snmpinterfaces.router_id) GROUP BY routers.router_ip");
$router_ips = array();
$router_devices = array();
$router_com = array();

while($router = $routers->fetch_object()){
    $router_ips[$router->routers_id] = $router->router_ip;
    $router_com[$router->routers_id] = Database::$db->query("SELECT trap_community FROM router_snmp_detail WHERE router_id=".$router->routers_id)->fetch_object()->trap_community;
}


foreach($router_ips as $id => $router_ip){
    
    $router_devices[$id]['sectime'] = snmptime(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.1.3'));
    
}

$microtime = microtime(true);


function interfaces($interfaces){
    
    $realInterfaces = array();
    foreach($interfaces as $key=>$value){
        $key = explode('.', $key);
        $realInterfaces[array_pop($key)] = trim(str_replace(array('"', "'", "STRING:"), '', $value));
    }    
    return $realInterfaces;
    
}


function bytes($bytes){
    
    $realBytes = array();
    foreach($bytes as $key=>$value){
        $key = explode('.', $key);
        $realBytes[array_pop($key)] = trim(str_replace(array('"', "'", "Counter32:", "Counter64:"), '', $value)) ;
    }    
    return $realBytes;
    
}


 foreach($router_ips as $id => $router_ip){
    
                $router_devices[$id]['bytesIn'] = bytes(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.2.2.1.10'));
                $router_devices[$id]['bytesOut'] = bytes(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.2.2.1.16'));
                $router_devices[$id]['interfaces'] = interfaces(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.2.2.1.2'));

}

/*$firstCircle = 1;

while(1){
    if($firstCircle==0){
        
                        $processTime = microtime(true);
                                                                
                        $newmicrotime = microtime(true) - $microtime;
                        $microtime = microtime(true);      
                        
                        flush();
                        ob_flush();
                        flush();
                        echo $newmicrotime . "<hr />";
                        flush();
                        ob_flush();
                        flush();
                      

$firstCircle = 0;
//echo "Proc Time: ".(microtime(true) - $processTime) ."<hr />";
//sleep(1);
            usleep(1000000 - ((microtime(true) - $processTime) * 1000001));
}*/


$pasttime = time();
while(1){
    
        if($pasttime < time()){
        
                                foreach($router_ips as $id => $router_ip){
                                                    
                                                            $router_devices[$id]['newsectime'] = snmptime(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.1.3'));
                                                            $router_devices[$id]['newbytesIn'] = bytes(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.2.2.1.10'));
                                                            $router_devices[$id]['newbytesOut'] = bytes(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.2.2.1.16'));
                                                            $router_devices[$id]['newinterfaces'] = interfaces(snmprealwalk($router_ip, $router_com[$id], '1.3.6.1.2.1.2.2.1.2'));


                                                            $speeds = array();
                                                            $rxs = array();
                                                            $txs = array();
                                                            
                                                            foreach($router_devices[$id]['interfaces'] as $interfaceKey => $interfaceName){
                                                                
                                                                $calculatedTime = ($router_devices[$id]['newsectime'] - $router_devices[$id]['sectime']);
                                                                
                                                                $speeds[$interfaceKey]['name'] = $interfaceName;

                                                                //if(($router_devices[$id]['newbytesIn'][$interfaceKey] - $router_devices[$id]['bytesIn'][$interfaceKey]) > 0){
                                                                    $speeds[$interfaceKey]['rx'] = ($router_devices[$id]['newbytesIn'][$interfaceKey] - $router_devices[$id]['bytesIn'][$interfaceKey]) /  128 ;
                                                                    $rxs[$interfaceKey] = round($speeds[$interfaceKey]['rx'], 2);
                                                                /*}else{
                                                                    $speeds[$interfaceKey]['rx'] = 0;
                                                                    $rxs[$interfaceKey] = 0;
                                                            	}*/
                                                            
                                                                //if(($router_devices[$id]['newbytesOut'][$interfaceKey] - $router_devices[$id]['bytesOut'][$interfaceKey]) > 0){
                                                                    $speeds[$interfaceKey]['tx'] = ($router_devices[$id]['newbytesOut'][$interfaceKey] - $router_devices[$id]['bytesOut'][$interfaceKey]) /  128 ;
                                                                    $txs[$interfaceKey] = round($speeds[$interfaceKey]['tx'], 2);
                                                                /*}else{
                                                                    $speeds[$interfaceKey]['tx'] = 0;
                                                                    $txs[$interfaceKey] = 0;
                                                                }*/
                                                            
                                                        }

                                                                
                                                                    
                                                    	foreach($router_devices[$id]['interfaces'] as $key=>$value){

                                                                // update snmpinterfaces interface_name field
                                                                table('snmpinterfaces')->fields(array('interface_name' => $value))->where(array("interface_id=$key", "router_id=$id"))->update();

                                                                // save temp snmp for live monitoring    
                                                                    $exists = Database::$db->query("SELECT * FROM snmptemp WHERE router_id=$id AND interface_id=$key")->num_rows;
                                                                    if($exists==1){
                                                                        $fields = array(
                                                                            'interface_name' => $value,
                                                                            'rx' => $rxs[$key],
                                                                            'tx' => $txs[$key]
                                                                        );
                                                                        table('snmptemp')->fields($fields)->where(array("router_id=$id", "interface_id=$key"))->update();
                                                                    }else{
                                                                        $fields = array(
                                                                            'interface_id' => $key,
                                                                            'interface_name' => $value,
                                                                            'rx' => $rxs[$key],
                                                                            'tx' => $txs[$key],
                                                                            'router_id' => $id
                                                                        );
                                                                        table('snmptemp')->fields($fields)->insert();
                                                                    }
                                                                    
                                                        }
                                                           

                                                    $router_devices[$id]['bytesIn'] = $router_devices[$id]['newbytesIn'];
                                                    $router_devices[$id]['bytesOut'] = $router_devices[$id]['newbytesOut'];
                                                    $router_devices[$id]['interfaces'] = $router_devices[$id]['newinterfaces'];
                                                    $router_devices[$id]['sectime'] = $router_devices[$id]['newsectime'];
                                                
                     }    
                         
                    $pasttime = time();
                            
                    
        }
    
    usleep(25);
    
    }


?> 