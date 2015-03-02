<?php
/**
 * Created by PhpStorm.
 * User: mohammad falahat
 * Date: 11/28/2014
 * Time: 10:50 PM
 */

require_once "db.php";
set_time_limit(0);
$lines = file('oui.txt');

$row = array();
$linenum = -1;

foreach($lines as $line){

    if(strpos($line, '(hex)')!==false){

        $linenum = -1;

        if(count($row)>0) {
            @$row['madein'] = array_pop($row['info']);
            $row['info'] = trim(implode("", $row['info']));
            echo "<pre style='font-family:calibri; color:gray'>" . print_r($row, 1) . "</pre>";
            table('brands')->fields($row)->insert();
        }

        $info = explode('(hex)', $line);
        $row = array(
            'hex' => trim($info[0]),
            'company' => trim($info[1])
        );

    }elseif(strpos($line, '(base 16)')!==false){

        $info = explode('(base 16)', $line);
        $row['base16'] = trim($info[0]);
        //$row['info'][$linenum] = trim($info[1])."\r\n";

    }else{

            if(strlen(trim($line))>0)
                $row['info'][$linenum] = trim($line)."\r\n";

    }

    $linenum++;

}
