aa
<?php
/**
 * Created by PhpStorm.
 * User: mohammad falahat
 * Date: 12/10/2014
 * Time: 10:51 AM
 */



// database
set_include_path(realpath(dirname(__FILE__)));
require_once("db.php");


// collect all of the unknown new ips
$query = "  SELECT target_ip FROM firewall
            WHERE target_ip NOT IN
            ( SELECT ip as target_ip FROM whois)
            GROUP BY target_ip";
$target_ips = Database::$db->query($query);


// fetch information of target ips one by one
while($row = $target_ips->fetch_object()){

    $ip = trim($row->target_ip);
    // Generate whois API Query
    $api_query = "http://www.whoisxmlapi.com/whoisserver/WhoisService?domainName=".$ip."&username=falahatme&password=mysql123";
    $api_content = file_get_contents($api_query) or die('Error in getting contents');
    $xml = new SimpleXMLElement($api_content) or die("error in reading xml");

echo $api_query;
    $whois_record= array('ip' => $ip);
    
    
    @$organization = $xml->xpath('registrant/organization');
    if(@$organization[0]->saveXML())
        $whois_record['organization'] = strip_tags($organization[0]->saveXML());
    
    @$country = $xml->xpath('registrant/country');
    if(@$country[0]->saveXML())
        $whois_record['country'] = strip_tags($country[0]->saveXML());
    
    @$telephone = $xml->xpath('administrativeContact/organization');
    if(@$telephone[0]->saveXML())
        $whois_record['telephone'] = strip_tags($telephone[0]->saveXML());

    

    table('whois')->fields($whois_record)->insert();
    usleep(1000);
    // unset variables for collecting memory
}

    unset($api_query);
    unset($whois_record);
    unset($xml);
    
?>
bb