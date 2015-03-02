<?php

$router_id = intval($_GET['router_id']);

if($router_id==0) die();

$router = Database::$db->query("SELECT * FROM routers WHERE routers_id=$router_id")->fetch_object();

$routersnmp = Database::$db->query("SELECT * FROM router_snmp_detail WHERE router_id=$router_id")->fetch_object();

$interfaces = Database::$db->query("SELECT * FROM snmpinterfaces WHERE router_id=$router_id AND (active = 1 or bytesactive=1) ORDER BY interface_type, interface_name");

?>


<div class="row">
	<div id="breadcrumb" class="col-xs-12">
		<a href="#" class="show-sidebar">
			<i class="fa fa-bars"></i>
		</a>
		<ol class="breadcrumb pull-left">
			<li><a href="index.html">Dashboard</a></li>
			<li><a href="#">Charts</a></li>
			<li><a href="#">Flot graphs</a></li>
		</ol>

	</div>
</div>


<div class="row">











<div id="dashboard_tabs" class="col-xs-12 col-sm-12">
		<!--Start Dashboard Tab 4-->
		<div id="dashboard-servers" class="row" style="visibility: visible; position: relative;">
			<div class="col-xs-12 col-sm-6 col-md-12 ow-server">
				<h4 class="page-header text-right"><img style="float:left; height:22px" src="<?php echo SITE_URL?>img/icons/mikrotik.png" /><span style="color:red; font-weight:600; font-style:italic"><?php echo $router->router_identity?></span> <span style="font-style: italic;">" <?php echo $router->router_location?> "</span></h4>
				<div class="row ow-server-bottom">
					<div class="col-sm-2">
						<div class="knob-slider">
							<div style="display:inline;width:90px;height:90px;"><input style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px none; background: none repeat scroll 0% 0% transparent; font: bold 12px Arial; text-align: center; color: rgb(106, 166, 214); padding: 0px;" id="knob-srv-1" class="knob" data-width="90" data-height="90" data-angleoffset="100" data-fgcolor="#6AA6D6" data-skin="tron" data-thickness=".2" value=""></div><br />CPU Load
						</div>
					</div>
					<?php
					$Memory = snmprealwalk($router->router_ip, $routersnmp->trap_community, '1.3.6.1.2.1.25.2.3.1.5');
					$totMem = 0;
					foreach($Memory as $mem){
						$totMem = intval(trim(str_replace('INTEGER:', '', $mem)));
						break;
					}
					?>
					<div class="col-sm-2">
						<div class="knob-slider">
							<div style="display:inline;width:90px;height:90px;"><input style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px none; background: none repeat scroll 0% 0% transparent; font: bold 12px Arial; text-align: center; color: rgb(106, 166, 214); padding: 0px;" id="knob-srv-2" class="knob" data-width="90" data-height="90"data-min="0" data-max="<?php echo $totMem/1024; ?>" data-fgcolor="#6AA6D6" data-skin="tron" data-thickness=".2" value=""></div><br />Memory Load
						</div>
					</div>
					<div class="col-sm-4">
					<?php
					$system = snmprealwalk($router->router_ip, $routersnmp->trap_community, '1.3.6.1.2.1.1');
					//print_r($system);
					$routeros = snmprealwalk($router->router_ip, $routersnmp->trap_community, '1.3.6.1.2.1.1.1.0');
					$uptime = snmprealwalk($router->router_ip, $routersnmp->trap_community, '1.3.6.1.2.1.1.3.0');
					$os_version = snmprealwalk($router->router_ip, $routersnmp->trap_community, '1.3.6.1.4.1.14988.1.1.4.4.0');
					?>
						<br />
						<div class="row"><i style="width:16px" class="fa fa-arrows-alt"></i> <b style="color:black">Router Os:</b> <?php echo trim(str_replace(array('"', "'", "STRING:"), '', array_pop($routeros)))?></div>
						<div class="row"><i style="width:16px" class="fa fa-bolt"></i> <b style="color:black">Uptime:</b> <span id="router<?php echo $router_id?>_uptime"></span></div>
						<div class="row"><i style="width:16px" class="fa fa-crosshairs"></i> <b style="color:black">Os Version:</b> <?php echo trim(str_replace(array('"', "'", "STRING:"), '', array_pop($os_version)))?></div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>

		</div>
		<!--End Dashboard Tab 4-->
	</div>





<hr style="border: 1px solid gray" />


</div>


					<div class="col-xs-12 col-sm-12">
							<!--Start Dashboard Tab 2-->
							<div id="dashboard-clients" class="row" style="visibility: visible; position: relative;">

								<div class="row one-list-message">
									<div class="col-xs-1"></div>
									<div class="col-xs-2"><b>Interface</b></div>
									<div class="col-xs-3"><b>Speed <span style="color:#0979E8">In</span> Current</b></div>
									<div class="col-xs-3"><b>Speed <span style="color:#35D442">Out</span> Current</b></div>
									<div class="col-xs-3"><b>Weekly Transmit (Kbyte)</b></div>
								</div>

								<?php
								while($row = $interfaces->fetch_object()){
								?>
								<div class="row one-list-message">
									<div class="col-xs-1"><img src="<?php echo SITE_URL?>img/icons/<?php if(array_key_exists($row->interface_type, $interfaceTypeIcons)) echo $interfaceTypeIcons[$row->interface_type]; else echo $row->interface_type?>.png" /></div>
									<div class="col-xs-2"><a href="<?php echo SITE_URL?>graph/interfaceview.php?id=<?php echo $row->interface_local_id?>"><b><?php echo htmlentities($row->interface_name)?></b></a></div>
									<div class="col-xs-3"><span style="font-size:12pt; color:#0979E8" class="message-date m-speed-in<?php echo $row->interface_local_id?>">-</span></div>
									<div class="col-xs-3"><span style="font-size:12pt; color:#35D442" class="message-date m-speed-out<?php echo $row->interface_local_id?>">-</span></div>
									<div class="col-xs-3 td-graph<?php echo $row->interface_local_id?>"></div>
								</div>
								<?php
								}
								?>

							</div>
							<!--End Dashboard Tab 2-->
						</div>




<script>


$(document).ready(function() {
	<?php
	$interfaces = Database::$db->query("SELECT * FROM snmpinterfaces WHERE router_id=$router_id AND active = 1 or bytesactive=1 ORDER BY interface_type, interface_name");
	while($row = $interfaces->fetch_object()){
	?>


	function DrawSparkline<?php echo $row->interface_local_id?>(){


<?php

$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=".$row->interface_id."  AND `router_id`=".$row->router_id."
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 7";

$table = Database::$db->query($Query);
$array = array();
$id = 8;
while($usage = $table->fetch_object()){
	$id--;
	$array[] = "[$id, ".($usage->in+$usage->out)."]";

}
$array = array_reverse($array);

?>

		var sparkline_table = [ <?php echo implode(', ', $array)?> ];


		$('.td-graph<?php echo $row->interface_local_id?>').each(function(){
			var arr = $.map( sparkline_table, function(val, index) {
				return [[val[0], [val[1]]]];
			});
			$(this).sparkline( arr ,
				{defaultPixelsPerValue: 10, minSpotColor: null, maxSpotColor: null, spotColor: null,
				fillColor: false, lineWidth: 1, lineColor: '#5A8DB6'});
			});

	}

	// Load Sparkline plugin and run callback for draw Sparkline charts for dashboard(top of dashboard + plot in tables)
	LoadSparkLineScript(DrawSparkline<?php echo $row->interface_local_id?>);


	// speed in and out live
	function speedin<?php echo $row->interface_local_id?>(){

		var interface_id = <?php echo $row->interface_local_id?>

		var rx = 0
	    $.ajax({
	        url: site_url+"snmpval.php",
	        data : {
	                    'iid' : interface_id,
	                    'type' : 'rx'
	        }
	        //async:false
	    }).done(function(ret) {

	        rx = ret;
	        if(rx.length<1)
	            rx=0;
	        rx = Math.round(rx);
	        
            $('.m-speed-in'+interface_id).html(bitProc(rx))
            
        });

	    setTimeout(speedin<?php echo $row->interface_local_id?>, 500);

	}

	function speedout<?php echo $row->interface_local_id?>(){

		var interface_id = <?php echo $row->interface_local_id?>

		var tx = 0
	    $.ajax({
	        url: site_url+"snmpval.php",
	        data : {
	                    'iid' : interface_id,
	                    'type' : 'tx'
	        }
	        //async:false
	    }).done(function(ret) {

	        tx = ret;
	        if(tx.length<1)
	            tx=0;
	        tx = Math.round(tx);
	        
            $('.m-speed-out'+interface_id).html(bitProc(tx))
            
        });

	    setTimeout(speedout<?php echo $row->interface_local_id?>, 500);

	}

	speedin<?php echo $row->interface_local_id?>();
	speedout<?php echo $row->interface_local_id?>();


	<?php
	}
	?>
});


//
// Redraw Knob charts on Dashboard (panel- servers)
//
function RedrawKnobcpu<?php echo $router_id?>(elem){
		

	    $.ajax({
        url: site_url+"snmprouterval.php",
        data : {
                    'router_id' : <?php echo $router_id?>,
                    'type' : 'cpu'
        }
        //async:false
    }).done(function(ret) {

			elem.animate({
				value: Math.floor(ret)
			},{
				duration: 100,
				easing:'swing',
				progress: function()
				{
					$(this).val(parseInt(Math.ceil(elem.val()))).trigger('change');
				}
			});

    });

}


//
// Redraw Knob charts on Dashboard (panel- servers)
//
function RedrawKnobmem<?php echo $router_id?>(elem){
		

	    $.ajax({
        url: site_url+"snmprouterval.php",
        data : {
                    'router_id' : <?php echo $router_id?>,
                    'type' : 'memory'
        }
        //async:false
    }).done(function(ret) {

			elem.animate({
				value: Math.floor(ret/1024)
			},{
				duration: 100,
				easing:'swing',
				progress: function()
				{
					$(this).val(parseInt(Math.ceil(elem.val()))).trigger('change');
				}
			});

    });

}


//
// Draw Knob Charts for Dashboard (for servers)
//
function DrawKnobDashboard<?php echo $router_id?>(){

	var srv_monitoring_cpu_selectors = [
		$("#knob-srv-1")/*,$("#knob-srv-3"),
		$("#knob-srv-4"),$("#knob-srv-5"),$("#knob-srv-6")*/
	];
	var srv_monitoring_mem_selectors = [
		$("#knob-srv-2")/*,$("#knob-srv-3"),
		$("#knob-srv-4"),$("#knob-srv-5"),$("#knob-srv-6")*/
	];

	srv_monitoring_cpu_selectors.forEach(DrawKnob);
	setInterval(function(){
		srv_monitoring_cpu_selectors.forEach(RedrawKnobcpu<?php echo $router_id?>);
	}, 500);

	srv_monitoring_mem_selectors.forEach(DrawKnob);
	setInterval(function(){
		srv_monitoring_mem_selectors.forEach(RedrawKnobmem<?php echo $router_id?>);
	}, 500);



}

	function UpdateUptime(){

	    $.ajax({
	        url: site_url+"snmprouterval.php",
	        data : {
	                    'router_id' : <?php echo $router_id?>,
	                    'type' : 'uptime'
	        }
	        //async:false
	    }).done(function(ret) {

				$('#router<?php echo $router_id?>_uptime').html(ret)

	    });

	    setTimeout(UpdateUptime, 500);

	}

	setTimeout(UpdateUptime, 500);

	// Load Knob plugin and run callback for draw Knob charts for dashboard(tab-servers)
	LoadKnobScripts(DrawKnobDashboard<?php echo $router_id?>);


</script>