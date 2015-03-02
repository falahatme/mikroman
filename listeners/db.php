<?php
/**
 * Created by PhpStorm.
 * User: mohammad falahat
 * Date: 11/26/2014
 * Time: 10:54 PM
 */

class Database{

    public static $db;

    public static function connect(){
        @ self::$db = new mysqli('p:localhost', 'root', '!@#$%^', 'mikroman');
        while(@$mysqli->connect_errno){
            sleep(1);
            self::connect();
            @self::$db->set_charset("utf8");
            @self::$db->query("SET GLOBAL timezone = '+3:30'");
            }
    }

}



Database::connect();


require_once "table.php";
function table($tblName){
    return new table($tblName);
}

