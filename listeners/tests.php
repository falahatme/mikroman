<?php
error_reporting(E_ALL);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

set_time_limit(0);


// INTERFACE INFORMATION

var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.2.2.1.7')); // test 

//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.1')); // system info
//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.2.2.1.2')); // interface info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.6')); // interface mac info
//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.31.1.1.1.10')); // interface RX (in)usage info in bytes
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.31.1.1.1.10')); // interface TX (out) usage  info in bytes
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.4.20')); // interface  ip info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.1.3.0')); // system uptime info
//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.25.3.3.1.2.1')); // cpu upsage info
//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.25.2.3.1.6')); // memory upsage info
//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.25.2.3.1.5')); // memory total info
//var_export(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.25.2.3.1.5')); // memory total info
//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.2.2.1.7')); // admin status info [2=disabled, 1=enabled]
//var_export(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.2.2.1.8')); // operation status info [2=disabled, 1=enabled]
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

function status($interfaces){
    
    $realInterfaces = array();
    foreach($interfaces as $key=>$value){
        $key = explode('.', $key);
        $realInterfaces[array_pop($key)] = trim(str_replace(array('"', "'", "INTEGER:"), '', $value));
    }    
    return $realInterfaces;
    
}


print_r(status(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.2.2.1.7')));
print_r(status(snmprealwalk('192.168.1.1', 'public10900898989898898989', '1.3.6.1.2.1.2.2.1.8')));


//print_r(interfaces(snmprealwalk('192.168.1.1', 'public', '1.3.6.1.2.1.2.2.1.2')));


/*
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


require_once('../ssh.php');

$ssh1 = new ssh('192.168.1.1', 'mikroman', 'mikroman');

function inTrim($string){
    while(strpos($string, '  ')!==false){
        $string = str_replace('  ', ' ', $string);
    }
    return $string;
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
        echo "<pre>".print_r($webProxy, 1)."</pre>";





// 01  IP ADDRESS PRINT in array $address_print
$result = $ssh1->exec("interface print detail", true);
print_r($result);
$result = explode("\n", $result);
array_shift($result);
$result = implode("\n", $result);
$result = explode("\r\n\r\n", $result);


$interfaces = array();
$result_count = count($result);
for($i=0 ; $i<$result_count ; $i++){
    
    $row =  $result[$i];
    $row = inTrim(str_replace("\r\n", ' ', $row));
    $row = explode('=', trim($row));

print_r($row);
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


//echo "<pre>".print_r($interfaces, 1)."</pre>";

