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




// INTERFACE INFORMATION

//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.1.3')); // test 

//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.1')); // system info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.2')); // interface info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.6')); // interface mac info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.31.1.1.1.10')); // interface RX (in)usage info in bytes
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.31.1.1.1.10')); // interface TX (out) usage  info in bytes
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.4.20')); // interface  ip info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.1.3.0')); // system uptime info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.25.3.3.1.2.1')); // cpu upsage info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.25.2.3.1.6')); // memory upsage info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.25.2.3.1.5')); // memory total info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.25.2.3.1.5')); // memory total info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.7')); // admin status info [2=disabled, 1=enabled]
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.8')); // operation status info [2=disabled, 1=enabled]
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.13')); // discard - lost in packets
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.19')); // discard - lost out packets
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.14')); // error in packets
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.20')); // error out packets
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.4')); // action mtu

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



while(1){
    
    
                $processTime = microtime();
        
    
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
                                                

                                                if(@($router_devices[$id]['newbytesIn'][$interfaceKey] - $router_devices[$id]['bytesIn'][$interfaceKey]) > 0){
                                                        $speeds[$interfaceKey]['rx'] = ($router_devices[$id]['newbytesIn'][$interfaceKey] - $router_devices[$id]['bytesIn'][$interfaceKey]) /  128;
                                                        $rxs[$interfaceKey] = round($speeds[$interfaceKey]['rx'], 2);
                                                }else{
                                                    $speeds[$interfaceKey]['rx'] = 0;
                                                    $rxs[$interfaceKey] = 0;
                                                }
                                                if(@($router_devices[$id]['newbytesOut'][$interfaceKey] - $router_devices[$id]['bytesOut'][$interfaceKey]) > 0){
                                                        $speeds[$interfaceKey]['tx'] = ($router_devices[$id]['newbytesOut'][$interfaceKey] - $router_devices[$id]['bytesOut'][$interfaceKey]) /  128;
                                                        $txs[$interfaceKey] = round($speeds[$interfaceKey]['tx'], 2);
                                                }else{
                                                    $speeds[$interfaceKey]['tx'] = 0;
                                                    $txs[$interfaceKey] = 0;
                                                }
                                            
                                        }

                                        
                                        foreach($router_devices[$id]['interfaces'] as $key=>$value){
                                                
                                                $active = table('snmpinterfaces')->where(array('router_id='.$id, 'interface_id='.$key, '`bytesactive`=1'))->run();
                                                if($active->num_rows==1){
                                                        $fields = array(
                                                            'interface_id' => $key,
                                                            'interface_name' => $value,
                                                            'in' => $rxs[$key],
                                                            'out' => $txs[$key],
                                                            'router_id' => $id
                                                        );
                                                        table('snmpbytelog')->fields($fields)->insert();

                                                }

                                        }     
                                           

                                        $router_devices[$id]['bytesIn'] = $router_devices[$id]['newbytesIn'];
                                        $router_devices[$id]['bytesOut'] = $router_devices[$id]['newbytesOut'];
                                        $router_devices[$id]['interfaces'] = $router_devices[$id]['newinterfaces'];
                                        $router_devices[$id]['sectime'] = $router_devices[$id]['newsectime'];
                                        

                }
                                    sleep(120);
}



?> 