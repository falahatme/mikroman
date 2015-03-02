<?php

/**
 * Created by PhpStorm.
 * User: mohammad falahat
 * Date: 12/08/2014
 * Time: 01:59 PM
 */

$ip = $_GET['ip'];

$query = "http://www.whoisxmlapi.com/whoisserver/WhoisService?domainName=$ip&username=falahatme&password=mysql123";

$xml = new SimpleXMLElement(file_get_contents($query));

echo @$xml->xpath('registrant/organization')[0]->saveXML()."<br />";
echo @$xml->xpath('registrant/country')[0]->saveXML()."<br />";
echo @$xml->xpath('administrativeContact/telephone')[0]->saveXML()."<br />";




