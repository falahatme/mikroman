<?php 


$interface_id = intval($_GET['iid']);
$interface = table('snmpinterfaces')->where(array("interface_local_id=$interface_id"))->run()->fetch_object();
$interface_id = $interface->interface_id;
$router_id = $interface->router_id;

// For Hourly Report

$Query = "SELECT *, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp FROM snmpbytelog WHERE `interface_id`=$interface_id AND `router_id`=$router_id ORDER BY timestamp DESC LIMIT 0, 30";

$result['hourly'] = Database::$db->query($Query);



// For Daily Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d %h' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 24";

$result['daily'] = Database::$db->query($Query);



// For Weekly Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 7";

$result['weekly'] = Database::$db->query($Query);



// For Monthly Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 30";

$result['monthly'] = Database::$db->query($Query);



// For Yearly Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 12";

$result['yearly'] = Database::$db->query($Query);



?>



<?php
$interfaces = table('snmpbytetemp')->where(array('active=1'))->run();
//$interface = table('snmpinterfaces')->where(array("`interface_local_id`=$interface_id", 'bytesactive=1'))->run()->fetch_array();


$periods = array(
        'hourly', 'daily', 'weekly', 'monthly', 'yearly'
    );

$avgs = array(
        'hourly' => '2 minutes', 'daily' => '1 hour', 'weekly' => '1 day', 'monthly' => '1 day', 'yearly' => '1 month'
    );


?>

<div class="row">

	<div id="breadcrumb" class="col-xs-12">
		<a href="#" class="show-sidebar">
			<i class="fa fa-bars"></i>
		</a>
		<ol class="breadcrumb pull-left">
			<li><a href="index.html">Dashboard</a></li>
			<li><a href="#">Charts</a></li>
			<li><a href="#">Morris Charts</a></li>
		</ol>
		<div id="social" class="pull-right">
			<a href="#"><i class="fa fa-google-plus"></i></a>
			<a href="#"><i class="fa fa-facebook"></i></a>
			<a href="#"><i class="fa fa-twitter"></i></a>
			<a href="#"><i class="fa fa-linkedin"></i></a>
			<a href="#"><i class="fa fa-youtube"></i></a>
		</div>
	</div>
    
</div>







<div class="row">

<?php
foreach($avgs as $period => $time){
?>

	<div class="col-xs-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-search"></i>
					<span>Interface <span style="color:red; font-weight:600"><?php echo $interface->interface_name?></span> (<?php echo $period ?>) In/Out Chart (Sum: <?php echo $time?>)</span>
				</div>
				<div class="box-icons">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
					<a class="expand-link">
						<i class="fa fa-expand"></i>
					</a>
					<a class="close-link">
						<i class="fa fa-times"></i>
					</a>
				</div>
				<div class="no-move"></div>
			</div>
			<div class="box-content">
				<div id="morris-chart-<?php echo $period?>" style="height: 200px;"></div>
			</div>
		</div>
	</div>
    
<?php
}
?>
    
</div>


<script type="text/javascript">






// Draw all test Morris Charts
function DrawAllMorrisCharts(){
    
    <?php
    
    foreach($periods as $period){
        ?>
        MorrisChart([<?php
                                            $records = array();
                                            while($row = $result[$period]->fetch_object()){
                                                switch($period){
                                                
                                                    case 'hourly':
                                                            $jsperiod = date("H:i", $row->unixtimestamp);
                                                    break;
                                                    
                                                    case 'daily':
                                                            $jsperiod = date("m-d H:i", $row->unixtimestamp);
                                                    break;
                                                    
                                                    case 'weekly':
                                                            $jsperiod = date("m-d", $row->unixtimestamp);
                                                    break;
                                                    
                                                    case 'monthly':
                                                            $jsperiod = date("m-d", $row->unixtimestamp);
                                                    break;
                                                    
                                                    case 'yearly':
                                                            $jsperiod = date("Y-m", $row->unixtimestamp);
                                                    break;
                                                    
                                                }
                                                $records[] = "{'period' : '$jsperiod', 'in' : ".$row->in.", 'out' : ".$row->out.", 'sum' : ".($row->in+$row->out)."}";
                                            }
                                            $records = array_reverse($records, true);
                                            echo implode(', ', $records);
                                            ?>], '<?php echo $period?>', ['in', 'out', 'sum'], ['In', 'Out', 'Sum']);
        <?php
    }
    
    ?>
    
    //MorrisChart( ['in', 'out', 'sum'], ['In', 'Out', 'Sum'])
    
}
$(document).ready(function() {
	// Load required scripts and draw graphs
	LoadMorrisScripts(DrawAllMorrisCharts);
	WinMove();
});
</script>

