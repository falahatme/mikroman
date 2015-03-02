<?php
/**
 * Created by PhpStorm.
 * User: paayab
 * Date: 12/03/2014
 * Time: 10:47 AM
 */
 
 
 
 function intval_checkbox($value){
     
     if($value=='on')
            $value = 1;
     return $value;
    
}
 
 
 function snmp2array($snmpArray, $delimeter){

    $realInterfaces = array();
    foreach($snmpArray as $key=>$value){
        $key = explode('.', $key);
        $realInterfaces[array_pop($key)] = trim(str_replace(array('>', '<', '"', "'", "$delimeter:"), '', $value));
    }    
    return $realInterfaces;
    
}
 
 
 
 function stringVariables($string){

        $variables = array();
         $string = trim($string);
         
         while(substr_count($string, '"')>1){
             
                $firstQuote = strpos($string, '"');
                $secondQuote = strpos($string, '"', $firstQuote+1);
                $tempval = substr($string, $firstQuote+1, $secondQuote-$firstQuote-1);
                
                $equal = $firstQuote;
                $space = strpos(strrev(substr($string, 0, $firstQuote)), ' ');
                $tempname = substr($string, $equal - $space, $space-1);
                
                $string = str_replace($tempname.'='.'"'.$tempval.'"', '', $string);
                $variables[$tempname] =$tempval;
                
    }
    
    
    $string = inTrim($string);
    $rows = explode(' ', $string);

    foreach($rows as $row){
        if(strpos($row, '=')!==false){
            list($key, $value) = explode('=', $row);
            $variables[$key] = $value;
        }
    }

    return $variables;
     
}


function router_mysql_interface_statuses($router_ip, $router_com){

    $adminStatus = snmp2array(snmprealwalk($router_ip, $router_com, '1.3.6.1.2.1.2.2.1.7'), "INTEGER");
    $operationStatus = snmp2array(snmprealwalk($router_ip, $router_com, '1.3.6.1.2.1.2.2.1.8'), "INTEGER");
    $statuses = array();
    foreach($adminStatus as $interface => $astatus){
        $statuses[$interface] = array('a' => $astatus, 'o' => array_shift($operationStatus));
    }
    return $statuses;
        
}
 
 
 function refresh_interfaces($router_id, $router_ip, $router_user, $router_pass, $router_ssh_port){
     
     
                    $interfaces = snmp_interface_types($router_ip, $router_user, $router_pass, $router_ssh_port);
                    table('router_interfaces')->where(array("router_id=$router_id"))->delete();

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
                
}

 
 
function interfaces($interfaces){
    
    $realInterfaces = array();
    foreach($interfaces as $key=>$value){
        $key = explode('.', $key);
        $realInterfaces[array_pop($key)] = trim(str_replace(array('"', "'", "STRING:"), '', $value));
    }    
    return $realInterfaces;
    
}



function router_snmp_interface_types($router_id, $trap_community){
    
    return interfaces(snmprealwalk($router_id, $trap_community, '1.3.6.1.2.1.2.2.1.2'));
    
}


function router_mysql_interface_types($router_id, $snmpitnerfaces){
    
    
    $interfaces = array();
    foreach($snmpitnerfaces as $snmpid => $snmpinterface){
        
            $type = table('router_interfaces')->fields(array('type'))->where(array("router_id=$router_id", "interface LIKE '$snmpinterface'"))->run()->fetch_object()->type;
             
             if(array_key_exists($type, $interfaces))
                $interfaces[$type]['count']++;
            else
                $interfaces[$type]['count']=1;
                $interfaces[$type]['names'][$snmpid] = $snmpinterface;
                    
    }
    
    return $interfaces;

}


function snmp_interface_types($router_ip, $username, $password, $port){
    
    
$ssh1 = new ssh($router_ip, $username, $password, $port);


// 01  IP ADDRESS PRINT in array $address_print
$result = $ssh1->exec("interface print detail", true);

$result = explode("\n", $result);
array_shift($result);
$result = implode("\n", $result);
$result = explode("\r\n\r\n", $result);


$interfaces = array();
$result_count = count($result);
for($i=0 ; $i<$result_count ; $i++){
    
    $row =  stringVariables($result[$i]); //print_r($row);
        
        if(strlen($row['type']) > 0 and strlen($row['name'])>0){
                    
                    if(array_key_exists($row['type'], $interfaces))
                        $interfaces[$row['type']]['count']++;
                    else
                        $interfaces[$row['type']]['count']=1;    
                        
                        $interfaces[$row['type']]['names'][] = $row['name'];
        
        }
}
    return $interfaces;
    
}



function inTrim($string){
    while(strpos($string, '  ')!==false){
        $string = str_replace('  ', ' ', $string);
    }
    return $string;
}


function showSize($size){

    if($size >= 1073741824){
        return round(($size/1073741824), 2)." GB";
    }elseif($size >= 1048576){
        return round(($size/1048576), 2)." MB";
    }elseif($size >= 1024){
        return round(($size/1024), 2)." KB";
    }else{
        return $size." B";
    }

}

function detectId($id){

    if(strpos($id, ':')!==false){
        return 'mac';
    }else{
        return 'ip';
    }

}

function pingDomain($domain){
    $starttime = microtime(true);
    @$file      = fsockopen ($domain, 80, $errno, $errstr, 1);
    $stoptime  = microtime(true);
    $status    = 0;

    if (!$file) $status = -1;  // Site is down
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
    return $status;
}




class functions{


    // Match username, numeric, email, url, date, checkbox
    /*

    e.g.p:
    functions::match('username', 'falah#at*me1'); // returns 'falahatme' (returns corrected string for username and numeric)
    functions::match('email', 'falah#at*me1'); // returns false (returns string if matched and false if not metched)

    */
    public static function match($type, $string, $captcha_val = null){

        switch($type){

            case 'username':

                return preg_replace("/[^A-Za-z0-9_]+/", '', $string);

                break;


            case 'numeric':

                return intval($string);

                break;


            case 'email':

                if(preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/', $string))
                    return $string;
                else
                    return false;

                break;


            case 'url':

                if(preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $string))
                    return $string;
                else
                    return false;

                break;


            case 'date':

                $string = str_replace(' ', '-', $string);
                $string = str_replace('/', '-', $string);
                $string = str_replace('\\', '-', $string);
                $string = str_replace('--', '-', $string);
                if(preg_match('/^([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})$/', $string))
                    return $string;
                else
                    return false;

                break;

            case 'checkbox':
                if($string)
                    return 1;
                return 0;
                break;


            /*
             *
             case 'captcha':

                if(@Session::$string() == $captcha_val){
                    Session::delete($string);
                    return true;
                }
                return false;

            break;
            */

        }

    }


    // Get users browser
    public static function get_browser(){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
            return 'IE';
        elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
            return 'Firefox';
        elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE)
            return 'Chrome';
        else
            return 'UNKNOWN';
    }



}
