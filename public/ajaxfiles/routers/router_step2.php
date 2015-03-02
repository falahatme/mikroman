

<h4 class="page-header">Step 2: &nbsp; &nbsp;



<?php


if(strlen(filter_var($_POST['router_ip'], FILTER_VALIDATE_IP))<1){
     die("<p>Invalid IP <img src='".SITE_URL."img/deny.png' /></p>");
}

$ssh1 = new ssh($_POST['router_ip'], $_POST['router_user'], $_POST['router_pass'], $_POST['router_ssh_port']);


if($ssh1->errno==2){
        echo "<span style=\"font-size:11pt;\">Connection <img src='".SITE_URL."img/deny.png' /></span>";
        die();
}elseif($ssh1->errno==3){
        echo "<span style=\"font-size:11pt;\">Connection <img src='".SITE_URL."img/allow.png' /> Authentication <img src='".SITE_URL."img/deny.png' /></span>";
        die();
}else{
        echo "<span style=\"font-size:11pt;\">Connection <img src='".SITE_URL."img/allow.png' /> Authentication <img src='".SITE_URL."img/allow.png' /></span>";
}
?>
</h4>

<?php


if(isset($_POST['com']) and $_POST['com']=='check'){
    


}elseif(isset($_POST['com']) and $_POST['com']=='add'){
    
    
    
    // Check in database
    $query = "SELECT * FROM routers WHERE router_identity='".Database::$db->real_escape_string($_POST['router_identity'])."'";
    $result = Database::$db->query($query);
    //die("Num Rows: ". $result->num_rows);
    if($result->num_rows >= 1){
        $wrongIdentity = true;
        //die("Identity is exist in database.");
    }else{
    
        
        $routerRow = array(
        'router_ip' => Database::$db->real_escape_string($_POST['router_ip']),
        'router_user' => Database::$db->real_escape_string($_POST['router_user']),
        'router_pass' => Database::$db->real_escape_string($_POST['router_pass']),
        'router_ssh_port' => Database::$db->real_escape_string($_POST['router_ssh_port']),
        'router_identity' => Database::$db->real_escape_string($_POST['router_identity']),
        'router_location' => Database::$db->real_escape_string($_POST['router_location'])
        );
        
        // Run SSH Queries
        
        $routerboard = explode("\n", $ssh1->exec("system routerboard print", true));
        //echo "<pre>".print_r($routerboard, 1)."</pre>";
        foreach($routerboard as $row){
                if(strpos($row, 'routerboard')!==false){
                    $routerRow['routerboard'] = trim(str_replace('routerboard:', '', $row));
                    }
                if(strpos($row, 'serial-number')!==false){
                    $routerRow['serial'] = trim(str_replace('serial-number:', '', $row));
                    }
        }
        
        
        $resource = explode("\n", $ssh1->exec("system resource print", true));
        //echo "<pre>".print_r($resource, 1)."</pre>";
        foreach($resource as $row){
                if(strpos($row, 'version')!==false){
                    $routerRow['os_version'] = trim(str_replace('version:', '', $row));
                    }
                if(strpos($row, 'architecture-name')!==false){
                    $routerRow['architecture_name'] = trim(str_replace('architecture-name:', '', $row));
                    }
                if(strpos($row, 'board-name')!==false){
                    $routerRow['board_name'] = trim(str_replace('board-name:', '', $row));
                    }
        }
        
                
                
        $license = explode("\n", $ssh1->exec("system license print", true));
        //echo "<pre>".print_r($license, 1)."</pre>";
        foreach($license as $row){
                if(strpos($row, 'software-id')!==false){
                    $routerRow['software_id'] = trim(str_replace('software-id:', '', $row));
                    }
                if(strpos($row, 'nlevel')!==false){
                    $routerRow['license_level'] = trim(str_replace('nlevel:', '', $row));
                    }
        }
        
        
        // Save Router Data On Database
        //print_r($routerRow);
        $router_id = table('routers')->fields($routerRow)->insert() or die("<p>Router is exist in database.</p>");
        
        echo "router added successfully, adding interfaces ...";
        flush();
        ob_flush();
        flush();
        
        $interfaces = snmp_interface_types(Database::$db->real_escape_string($_POST['router_ip']), Database::$db->real_escape_string($_POST['router_user']), Database::$db->real_escape_string($_POST['router_pass']), Database::$db->real_escape_string($_POST['router_ssh_port']));
        
        foreach($interfaces as $type => $information){
            foreach($information['names'] as $interface){
                $fields = array(
                    'router_id' => $router_id,
                    'type' => $type,
                    'interface' => $interface
                );
                table('router_interfaces')->fields($fields)->insert();
            }
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
    table('router_snmp_detail')->fields($snmp_detail_field)->insert();
        
    die("<script>location='".SITE_URL."routers/routers.php'</script>");
}
}





// 01  IP ADDRESS PRINT in array $address_print
$result = $ssh1->exec("interface print detail", true);
$result = explode("\n", $result);
array_shift($result);
$result = implode("\n", $result);
$result = explode("\r\n\r\n", $result);


$interfaces = array();
$result_count = count($result);
for($i=0 ; $i<$result_count ; $i++){
    
    $row =  $result[$i];
    $row = inTrim(str_replace("\r\n", ' ', $row));
    $row = explode(' ', trim($row));

    foreach($row as $field){

    if(strpos($field, '=')!==false){
            list($key, $value) = explode('=', str_replace('"', '', $field));
            if($key=='type'){
                if(strlen($value)>0){
                    if(array_key_exists($value, $interfaces))
                        $interfaces[$value]++;
                    else
                        $interfaces[$value]=1;
                    }
                }
            }
        }
        
    }






$identity = $ssh1->exec("system identity print", true);
$identity = trim(str_replace('name: ', '', $identity));


?>
            

	<div class="row">


    
    
        
		<div class="col-md-7" style="border-right:1px dashed silver">
        
                
            <div class="row">
            <?php
            foreach($interfaces as $interface=>$num){
            ?>
                <div class="col-md-6" style="padding:6px;">
                <img style="width:20px; height:20px;" src="<?php echo SITE_URL?>img/icons/<?php if(array_key_exists($interface, $interfaceTypeIcons)) echo $interfaceTypeIcons[$interface]; else echo $interface?>.png" /> <?php echo $interface?>: <?php echo $num?>
                </div>
             <?php
             }
             ?>
            </div>
        
        </div>
        
        
        
        
        
        <?php
        
        // CHECK SERVICES 
        

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

        
        
        // 01  IP PROXY FOR WEB PROXY
        $result = $ssh1->exec("ip proxy print", true);
        $result = explode("\n", $result);
        $webProxy = array();
        
        foreach($result as $row){
        
            $row = trim($row);
            if(strpos($row, ': ')){
                list($key, $value) = explode(': ', $row);
                $webProxy[$key] = $value;
            }
            
    }
    


    // FIREWALL NAT FOR PROXY TRANSPARENT 
    $webProxyTransparent = "no";
    if($webProxy['enabled']=='yes'){
        $result = $ssh1->exec("ip firewall nat print", true);
        $result = explode("\n", $result);
        $matchString = "chain=dstnat action=redirect to-ports=".$webProxy['port']." protocol=tcp dst-port=80";
        foreach($result as $row){
            if(strpos($row, $matchString)!==false and strpos($row, ' X ')==false){
                $webProxyTransparent = "yes";
                break;
            }
        }
    }
    
        
        // 01  IP HOTSPOT FOR HOTSPOT
        $result = $ssh1->exec("ip hotspot print", true);
        $result = explode("\n", $result);
        array_shift($result);
        array_shift($result);
        $result = array_filter($result);       
       
       $hostspotStatus = "red";
       foreach($result as $row){
           if(strlen($row)<2)
                continue;
           else{
               if(strpos($row, ' X ')!==false)
                    $hostspotStatus = "yellow";
                else
                    $hostspotStatus = "green";
                break;
            }
        }

        
        
        // 01  DNS 
        $result = $ssh1->exec("ip dns print", true);
        $result = explode("\n", $result);
        $dnsSettings = array();
        
        foreach($result as $row){
        
            $row = trim($row);
            if(strpos($row, ': ')){
                list($key, $value) = explode(': ', $row);
                $dnsSettings[$key] = $value;
            }
            
        }
        
        
        // 01  RADIUS FOR RADIUS CLIENT
        $result = $ssh1->exec("radius print", true);
        $result = explode("\n", $result);
        array_shift($result);
        array_shift($result);
        $result = array_filter($result);       
       
       $radiusStatus = "red";
       foreach($result as $row){
           if(strlen($row)<2)
                continue;
           else{
               if(strpos($row, ' X ')!==false)
                    $radiusStatus = "yellow";
                else
                    $radiusStatus = "green";
                break;
            }
    }
    
    
            
        // SNMP FOR GRAPH
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

        
?>
        
        
        
        
		<div class="col-md-5">
        
                
            <div class="row">
                <div class="col-md-12">
                
                <table>
                <tr style="margin:5px;">
                <td style="padding:5px;"><img style="width:24px; height:24px;" src="<?php echo SITE_URL?>img/icons/dhcp.png" /></td><td><a href="#">DHCP Server</a></td><td style="padding:5px 5px 5px 10px;"><img style="width:20px; height:20px; overflow:none;" src="<?php echo SITE_URL?>img/icons/<?php if(count($dhcp_services)>0) echo "green"; else echo "red"?>.png" /></td>
                </tr>
                <tr style="margin:5px;">
                <td style="padding:5px;"><img style="width:24px; height:24px;" src="<?php echo SITE_URL?>img/icons/dns.png" /></td><td><a href="#">DNS</a> </td><td style="padding:5px 5px 5px 10px;"><img style="width:20px; height:20px; overflow:none;" src="<?php echo SITE_URL?>img/icons/<?php if($dnsSettings['allow-remote-requests']=='yes') echo "green"; else echo "red"?>.png" /></td>
                </tr>
                <tr style="margin:5px;">
                <td style="padding:5px;"><img style="width:24px; height:24px;" src="<?php echo SITE_URL?>img/icons/radius-client.png" /></td><td><a href="#">RADIUS Client</a>  </td><td style="padding:5px 5px 5px 10px;"><img style="width:20px; height:20px; overflow:none;" src="<?php echo SITE_URL?>img/icons/<?php echo $radiusStatus?>.png" /></td>
                 </tr>                
                 <tr style="margin:5px;">
                <td style="padding:5px;"><img style="width:24px; height:24px;" src="<?php echo SITE_URL?>img/icons/hotspot.png" /></td><td><a href="#">HOTSPOT</a> </td><td style="padding:5px 5px 5px 10px;"><img style="width:20px; height:20px; overflow:none;" src="<?php echo SITE_URL?>img/icons/<?php echo $hostspotStatus?>.png" /></td>
                 </tr>
                 <tr style="margin:5px;">
                <td style="padding:5px;"><img style="width:24px; height:24px;" src="<?php echo SITE_URL?>img/icons/webproxy.png" /></td><td><a href="#"  class="proxytooltip" data-toggle="tooltip" data-placement="bottom" title="Transparent: <?php echo $webProxyTransparent?>" >Web Proxy</a> </td><td style="padding:5px 5px 5px 10px;"><img style="width:20px; height:20px; overflow:none;" src="<?php echo SITE_URL?>img/icons/<?php if($webProxy['enabled']=='yes') echo "green"; else echo "red"?>.png" /></td>
                </tr>
                <tr style="margin:5px;">
                <td style="padding:5px;"><img style="width:24px; height:24px;" src="<?php echo SITE_URL?>img/icons/snmp.png" /></td><td><a href="#"  class="proxytooltip" data-toggle="tooltip" data-placement="bottom">SNMP</a> </td><td style="padding:5px 5px 5px 10px;"><img style="width:20px; height:20px; overflow:none;" src="<?php echo SITE_URL?>img/icons/<?php if($snmp['enabled']=='yes') echo "green"; else echo "red"?>.png" /></td>
                </tr>
                </table>
                
                </div>
            </div>
        
        </div>
    
        
    
    
    
    
		<div class="col-md-10" style="border-top:1px dashed silver">
        
                   
                   <form class="form-horizontal" action="<?php echo SITE_URL?>subviews/routers/router_step2.php" id="router_step2" method="post" role="form">

                        <input type="hidden" name="com" value="add" />
                        <input type="hidden" name="router_ip" value="<?php echo $_POST['router_ip']?>" />
                        <input type="hidden" name="router_user" value="<?php echo $_POST['router_user']?>" />
                        <input type="hidden" name="router_pass" value="<?php echo $_POST['router_pass']?>" />
                        <input type="hidden" name="router_ssh_port" value="<?php echo $_POST['router_ssh_port']?>" />

                        <div class="form-group has-error">
                            <label class="col-sm-3 control-label">Router Identity</label>
                            <div class="col-sm-6">
                                <input type="text" name="router_identity" class="form-control" placeholder="Router IP Address" value="<?php echo $identity?>" required="required" data-toggle="tooltip" data-placement="bottom">
                            </div>
                            <?php
                            if($wrongIdentity){
                            ?>
                            <label class="col-sm-1 control-label"><img src="<?php echo SITE_URL?>img/deny.png" style="float:right; position:inherit; top-20px;" /></label>
                            <?php
                            }
                            ?>
                        </div>

                        <div class="form-group has-error">
                            <label class="col-sm-3 control-label">Location</label>
                            <div class="col-sm-6">
                                <input type="text" name="router_location" class="form-control" value="<?php echo @ $_POST['router_location'] ?>" placeholder="Router Location" required="required" data-toggle="tooltip" data-placement="bottom">
                            </div>
                        </div>



                        <div class="form-group has-error">
                        <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-primary btn-label-left">
                                    <span><i class="fa fa-clock-o"></i></span>
                                    Submit
                                </button>
                            </div>
                        </div>              
                        
                        </form>
        
        </div>
        
        
        
        
        
        
        
	</div>




<script>

$('.proxytooltip').tooltip();

</script>
