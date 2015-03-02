<?php


require "../listeners/db.php";


$interfaceId = intval($_GET['iid']);
$type = $_GET['type'];

if($type=="rx" or $type=="tx"){

            $result = table('snmptemp')->where(array("interface_local_id=$interfaceId"))->run()->fetch_object();


            switch($type){
                
                case 'rx':
                    $bytes = $result->rx;
                break;
                case 'tx':
                    $bytes = $result->tx;
                break;
                
            }

            echo $bytes;

}elseif($type=="in" or $type=="out"){
    
            $result = table('snmpbytetemp')->where(array("interface_local_id=$interfaceId"))->run()->fetch_object();


            switch($type){
                
                case 'in':
                    $bytes = $result->in;
                break;
                case 'out':
                    $bytes = $result->out;
                break;
                
            }

            echo $bytes;
    
}



?>