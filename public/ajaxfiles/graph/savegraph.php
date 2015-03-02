<?php


$router_id = intval($_POST['router_id']);

foreach($_POST['interface_id'] as $interface_id => $interface_name){
    
    $interface_id = intval($interface_id);
    $existance = Database::$db->query("SELECT count(*) as rows FROM snmpinterfaces WHERE interface_id=".$interface_id)->fetch_object()->rows;
    if($existance  == 1){
            $fields = array(
                    'interface_type' => Database::$db->real_escape_string($_POST['interface_type'][$interface_id]),
                    'interface_name' => Database::$db->real_escape_string($interface_name),
                    'active' => intval_checkbox($_POST['interface_active'][$interface_id]),
                    'router_id' => $router_id,
                    'bytesactive' => intval_checkbox($_POST['interface_bytesactive'][$interface_id])          
            );
            table('snmpinterfaces')->fields($fields)->where(array("interface_id=".$interface_id))->update();
    }else{
            $fields = array(
                    'interface_id' => $interface_id,
                    'interface_type' => Database::$db->real_escape_string($_POST['interface_type'][$interface_id]),
                    'interface_name' => Database::$db->real_escape_string($interface_name),
                    'active' => intval_checkbox($_POST['interface_active'][$interface_id]),
                    'router_id' => $router_id,
                    'bytesactive' => intval_checkbox($_POST['interface_bytesactive'][$interface_id])       
            );
            table('snmpinterfaces')->fields($fields)->insert();
    }
    
}

echo $router_id;

?>