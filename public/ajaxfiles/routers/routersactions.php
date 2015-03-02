

<?php

$router_id = intval($_GET['router_id']);
$action = Database::$db->real_escape_string($_GET['action']);


switch($action){

        case 'refresh':
    

                    $router = table('routers')->where(array("routers_id=$router_id"))->run()->fetch_object();
                    $ssh1 = new ssh($router->router_ip, $router->router_user, $router->router_pass, $router->router_ssh_port);


                    if($ssh1->errno==2){
                            echo "<span style=\"font-size:11pt;\">Connection <img src='".SITE_URL."img/deny.png' /></span>";
                            die();
                    }elseif($ssh1->errno==3){
                            echo "<span style=\"font-size:11pt;\">Connection <img src='".SITE_URL."img/allow.png' /> Authentication <img src='".SITE_URL."img/deny.png' /></span>";
                            die();
                    }
                
                
    
                    // ADD SNMP DETAIL AND INTERFACES
                            
                        $result = $ssh1->exec("snmp print", true);
                        $result = explode("\n", $result);
                        $snmp = array();
                        
                        foreach($result as $row){
                        
                            $row = trim($row);
                            if(strpos($row, ': ')){
                                list($key, $value) = explode(': ', $row);
                                $snmp[$key] = $value;
                            }
                            
                    }
        
        
                    // SNMP FOR GRAPH
                    $result = $ssh1->exec("snmp community print detail", true);

                    $result = explode("\n", $result);
                    array_shift($result);
                    $result = implode("\n", $result);
                    $result = explode("\r\n\r\n", $result);

                    $snmpcom = stringVariables($result[0]);
                    if(strpos($result[0], ' * ')!==false){
                        $snmpcom['community_default'] = 'yes';
                    }else{
                        $snmpcom['community_default'] = 'no';
                    }

                    $snmp_detail_field = array(
                    
                        'router_id' => $router_id,
                        'enabled' => $snmp['enabled'],
                        'contact' => $snmp['contact'],
                        'location' => $snmp['location'],
                        'engine_id' => $snmp['engine-id'],
                        'trap_target' => $snmp['trap-target'],
                        'trap_community' => $snmp['trap-community'],
                        'trap_version' => $snmp['trap-version'],
                        'trap_generators' => $snmp['trap-generators'],
                        'trap_interfaces' => $snmp['trap-interfaces'],
                        'community_default' => $snmpcom['community_default'],
                        'name' => $snmpcom['name'],
                        'address' => $snmpcom['addresses'],
                        'security' => $snmpcom['security'],
                        'read_access' => $snmpcom['read-access'],
                        'write_access' => $snmpcom['write-access'],
                        'authentication_protocol' => $snmpcom['authentication-protocol'],
                        'encrypt_protocol' => $snmpcom['encryption-protocol'],
                        'authentication_password' => $snmpcom['authentication-password'],
                        'encryption_password' => $snmpcom['encryption-password']
                        
                    );
                    table('router_snmp_detail')->where(array("router_id=$router_id"))->delete();
                    table('router_snmp_detail')->fields($snmp_detail_field)->insert();
                        
                    
                    refresh_interfaces($router_id, $router->router_ip, $router->router_user, $router->router_pass, $router->router_ssh_port);
                        
                    exec("sudo service mikroman-snmp restart; sudo service mikroman-snmpbytes restart; sudo service mikroman-snmp_live restart;");
                        
        break;
        
        case 'edit':
        
                die("Edit is not available at this time.");
        
        break;
        
        case 'delete':
        
                    table('router_interfaces')->where(array("router_id=$router_id"))->delete();
                    table('router_snmp_detail')->where(array("router_id=$router_id"))->delete();
                    table('routers')->where(array("routers_id=$router_id"))->delete();
        
        break;
    
}

die("<script>location='".SITE_URL."routers/routers.php'</script>");

?>