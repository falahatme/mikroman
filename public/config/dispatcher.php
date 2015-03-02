<?php


# Pathes

define('SITE_PATH',		str_replace(array('/config'), '', str_replace('\\', '/', realpath(dirname(__FILE__)))).'/');
define('SITE_URL',		'http://'.$_SERVER['HTTP_HOST'].rtrim(dirname(str_replace(array('/config'), '', $_SERVER['PHP_SELF'])), '/\\').'/');
define('SYSTEM_PATH',	SITE_PATH.'application/system/');


    $interfaceTypeIcons = array(
    
        'pppoe-out' => 'ppp-out',
        'pptp-out' => 'ppp-out',
        'l2tp-out' => 'ppp-out',
        'ovpn-out' => 'ppp-out',
        'sstp-out' => 'ppp-out',

        'pppoe-in' => 'ppp-in',
        'pptp-in' => 'ppp-in',
        'l2tp-in' => 'ppp-in',
        'ovpn-in' => 'ppp-in',
        'sstp-in' => 'ppp-in',

        'eoip' => 'tunnel',
        'ipip-tunnel' => 'tunnel',
        'gre-tunnel' => 'tunnel'

    );


?>