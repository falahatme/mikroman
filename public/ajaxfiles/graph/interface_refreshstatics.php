<?php


$type = Database::$db->real_escape_string($_GET['type']);
$period = Database::$db->real_escape_string($_GET['period']);

$router_id = intval($_GET['router_id']);
$interface_id = intval($_GET['interface_id']);


switch($type){

	case 'rxtx':

		// transmit processes
		switch($period){

			case 'hourly':

				// For Hourly Report

				$Query = "SELECT *, (`timestamp`) as unixtimestamp FROM snmplog WHERE `interface_id`=$interface_id AND `router_id`=$router_id ORDER BY timestamp DESC LIMIT 0, 30";

			break;			

			case 'daily':

				// For Daily Report

				$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d %H' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 24";

			break;			

			case 'weekly':

				$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 7";

			break;			

			case 'monthly':

				$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 30";

			break;			

			case 'yearly':

				$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 12";

			break;			

		}


		$result = Database::$db->query($Query);

		$rowArray = array();
        $rowR = array();
        $rowT = array();
		while($row = $result->fetch_object()){

			$rowArray[] = '{"rx": '.$row->rx.',"tx": '.$row->tx.', "unixtimestamp": '.strtotime($row->unixtimestamp).'}';
            $rowR[] = $row->rx;
            $rowT[] = $row->tx;

		}
        $rowArray = array_reverse($rowArray);


        echo "{";

			echo "\"data\" : [".implode(", ", $rowArray)."], \n";
			echo "\"num_rows\": " . $result->num_rows . ", \n";
			echo "\"maxR\": " . max($rowR) . ", \n";
			echo "\"maxT\": " . max($rowT) . ", \n";
			echo "\"currR\": " . $rowR[0] . ", \n";
			echo "\"currT\": " . $rowT[0] . ", \n";
			echo "\"sumR\": " . array_sum($rowR) . ", \n";
			echo "\"sumT\": " . array_sum($rowT);


		echo "}";


	break;

	case 'inout':

		// byte in processes
		switch($period){

			case 'hourly':

			break;			

			case 'daily':

			break;			

			case 'weekly':

			break;			

			case 'monthly':

			break;			

			case 'yearly':

			break;			

		}

	break;

}

die();


?>