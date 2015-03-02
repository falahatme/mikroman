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
		<div id="social" class="pull-right">
			<a href="#"><i class="fa fa-google-plus"></i></a>
			<a href="#"><i class="fa fa-facebook"></i></a>
			<a href="#"><i class="fa fa-twitter"></i></a>
			<a href="#"><i class="fa fa-linkedin"></i></a>
			<a href="#"><i class="fa fa-youtube"></i></a>
		</div>
	</div>
</div>



<?php 


$interface_id = intval($_GET['iid']);
$interface = table('snmpinterfaces')->where(array("interface_local_id=$interface_id"))->run()->fetch_object();
$interface_id = $interface->interface_id;
$router_id = $interface->router_id;

// For Hourly Report

$Query = "SELECT *, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp FROM snmplog WHERE `interface_id`=$interface_id AND `router_id`=$router_id ORDER BY timestamp DESC LIMIT 0, 30";

$result['hourly'] = Database::$db->query($Query);



// For Daily Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d %h' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        ORDER BY newtime DESC LIMIT 0, 24";

$result['daily'] = Database::$db->query($Query);



// For Weekly Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        ORDER BY newtime DESC LIMIT 0, 7";

$result['weekly'] = Database::$db->query($Query);



// For Monthly Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        ORDER BY newtime DESC LIMIT 0, 30";

$result['monthly'] = Database::$db->query($Query);



// For Yearly Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        ORDER BY newtime DESC LIMIT 0, 12";

$result['yearly'] = Database::$db->query($Query);



?>



<div class="row">


<?php
$interfaces = table('snmptemp')->where(array('active=1'))->run();
$interface = table('snmpinterfaces')->where(array("`interface_local_id`=$interface_id", 'active=1'))->run()->fetch_array();


$periods = array(
        'hourly', 'daily', 'weekly', 'monthly', 'yearly'
    );

$avgs = array(
        'hourly' => '2 minutes', 'daily' => '1 hour', 'weekly' => '1 day', 'monthly' => '1 day', 'yearly' => '1 month'
    );


?>



<?php

foreach($periods as $period){

?>
	<div class="col-xs-12 col-sm-6" style="min-height: 100px;">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa  fa-area-chart"></i>
					<span>Interface <span style="color:red; font-weight:600"><?php echo  $interface[interface_name]?></span> (<?php echo $period?>)  Graph (Avg: <?php echo $avgs[$period]?>)</span>
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
				<div id="box-<?php echo $period?>-content" style="min-height: 200px;"></div>
                
                                <div class="row" style="margin-top:20px">
                                
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Max <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="maxin_<?php echo $period?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Average <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="avgin_<?php echo $period?>">0</span>
                                                </div>
                                        </div>
                                                                        
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Current <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="curin_<?php echo $period?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Max <span style="color:#35D442">Out</span>: <span style="font-weight:600; color:#35D442" id="maxout_<?php echo $period?>">0</span>
                                                </div>
                                        </div>
                                                                        
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Average <span style="color:#35D442">Out</span>: <span style="font-weight:600; color:#35D442" id="avgout_<?php echo $period?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Current <span style="color:#35D442">Out</span>:  <span style="font-weight:600; color:#35D442" id="curout_<?php echo $period?>">0</span>
                                                </div>
                                        </div>
                                        
                                </div>
                
			</div>
		</div>
	</div>
    
<?php

}

?>






</div>
<script type="text/javascript">





//
// Graph1 created in element with id = box-one-content
//
function FlotGraph(interface_id, period, num_rows, rows, unixtimestamp){
    
    

    
	// We use an inline data source in the example, usually data would
	// be fetched from a server
    
switch(period){
    
    case 'hourly':
        
        var data = [],
        totalPoints = 60;	
        var datat = [],
        totalPoints = 60;
        num_rows *= 2;
        
    break;
    
    case 'daily':
        
        var data = [],
        totalPoints = 24;	
        var datat = [],
        totalPoints = 24;	
        
    break;
    
    case 'weekly':
        
        var data = [],
        totalPoints = 7;	
        var datat = [],
        totalPoints = 7;	
        
    break;
    
    case 'monthly':
        
        var data = [],
        totalPoints = 30;	
        var datat = [],
        totalPoints = 30;	
        
    break;
    
    case 'yearly':
        
        var data = [],
        totalPoints = 12;	
        var datat = [],
        totalPoints = 12;	
        
    break;
    
    
    case 'default':
        
        var data = [],
        totalPoints = 30;	
        var datat = [],
        totalPoints = 30;	
        
    break;
    
}    
    
    
    
var realdata = [];
var realtdata = [];



  /* function getRealData() {

		if (data.length > 0)
			data = data.slice(1);
		// Do a walk
    // Get Data From Ajax
    var rx = 0;
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
            //alert(rx)
       var prev = data.length > 0 ? data[data.length - 1] : 0,
        y = rx
        //

            while (data.length < totalPoints) {
                    var prev = data.length > 0 ? data[data.length - 1] : 0;
                    y = rx;

                if (y < 0) {
                    y = 0;
                } 
                data.push(y);
            }
            // Zip the generated y values with the x values
            var res = [];
            var sum=0;
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]])
                sum += data[i];
        }
        
            $('#curin_'+interface_id).html(bitProc(y))
            $('#maxin_'+interface_id).html(bitProc(Math.max.apply(Math, data)))
            $('#avgin_'+interface_id).html(bitProc((sum/data.length).toFixed(2)))
            
            realdata = res;
            
        });

	}



*/

/*function getRealTData() {

		if (data.length > 0)
			datat = datat.slice(1);
		// Do a walk
    // Get Data From Ajax
    var rx = 0;
    $.ajax({
        url: site_url+"snmpval.php",
        data : {
                    'iid' : interface_id,
                    'type' : 'tx'
        }
        //async:false
    }).done(function(ret) {
        rx = ret;
    if(rx.length<1)
        rx=0;
    rx = Math.round(rx);
        //alert(rx)
   var prev = datat.length > 0 ? datat[datat.length - 1] : 0,
    y = rx
    //

		while (datat.length < totalPoints) {
                var prev = datat.length > 0 ? datat[datat.length - 1] : 0;
                y = rx;

			if (y < 0) {
				y = 0;
			} 
			datat.push(y);
		}
		// Zip the generated y values with the x values
		var res = [];
        var sum = 0;
		for (var i = 0; i < datat.length; ++i) {
			res.push([i, datat[i]])
            sum += datat[i];
		}
        $('#curout_'+interface_id).html(bitProc(y))
        $('#maxout_'+interface_id).html(bitProc(Math.max.apply(Math, datat)))
        $('#avgout_'+interface_id).html(bitProc((sum/datat.length).toFixed(2)))
        realtdata = res;
    
    });
	}



*/

function getRandomData(type) {
    
    if(type=='rx'){
		if (data.length > 0)
			data = data.slice(1);
		// Do a random walk
		while (data.length < (totalPoints-num_rows)) {
                var prev = data.length > 0 ? data[data.length - 1] : 0,
                y = prev + 0;

			if (y < 0) {
				y = 0;
			} else if (y > 100) {
				y = 100;
			}
			data.push(y);
        }
    
        $.each(rows, function(index, element){
            data.push(Math.round(element.rx))
            if(period=='hourly')
                data.push(Math.round(element.rx))
        })        
    
    }

    if(type=='tx'){
		if (datat.length > 0)
			datat = datat.slice(1);
            // Do a random walk
            while (datat.length < (totalPoints-num_rows)) {
                    var prev = datat.length > 0 ? datat[datat.length - 1] : 0,
                    y = prev + 0;

                if (y < 0) {
                    y = 0;
                } else if (y > 100) {
                    y = 100;
            }
                datat.push(y);
        }
    
        $.each(rows, function(index, element){
            datat.push(Math.round(element.tx))
            if(period=='hourly')
                datat.push(Math.round(element.tx))
        })    
    
    }
    


		// Zip the generated y values with the x values
		var res = [];
        var sum = 0;
        if(type=='rx'){
            for (var i = 0; i < data.length; ++i) {
                
               switch(period){

                  default:
                                 res.push([i, data[i]])
                  break;
                   
                }                
                sum += data[i]
                
            }
        }else{
            for (var i = 0; i < datat.length; ++i) {
                
               switch(period){
                  
                  default:
                                 res.push([i, datat[i]])
                  break;
                   
                }                
                sum += datat[i]
                
            }
    }

        $('#curin_'+period).html(bitProc(data[data.length-1]))
        $('#maxin_'+period).html(bitProc(Math.max.apply(Math, data)))
        $('#avgin_'+period).html(bitProc((sum/data.length).toFixed(2)))
        $('#curout_'+period).html(bitProc(datat[datat.length-1]))
        $('#maxout_'+period).html(bitProc(Math.max.apply(Math, datat)))
        $('#avgout_'+period).html(bitProc((sum/datat.length).toFixed(2)))
    
    
		return res;
	}


switch(period){

        case 'yearly':
            var xaxis =  { 
                        show: true,
                         axisLabel: "Date",
                        axisLabelUseCanvas: true,
                        axisLabelFontSizePixels: 12,
                        axisLabelFontFamily: 'Verdana, Arial',
                        axisLabelPadding: 10
            }
        break;
        
        default:
            var xaxis =  { 
                        show: true
            }
        break;


}

	var updateInterval = 1000;
	var plot = $.plot("#box-"+period+"-content", 
                                [ 
                                    {data: getRandomData('rx'), label: " RX = 0 kb "},
                                    {data: getRandomData('tx'), label: " TX = 0 kb "}
                                ], {
		series: {
			shadowSize: 0,	// Drawing is faster without shadows
            lines: {
                fill: true
            }
		},
    grid: {
			hoverable: true
    },
    colors: ["#0979E8", "#35D442"], // rx, tx
    yaxis: {min: 0,	max: 3000},
    xaxis: xaxis,

    
	});




// On Hover

var legends = $("#box-"+period+"-content .legendLabel");
	legends.each(function () {
		// fix the widths so they don't jump around
		$(this).css({'width': 80, 'float': 'left'});
	});
	var updateLegendTimeout = null;
	var latestPosition = null;
	function updateBWLegend() {
		updateLegendTimeout = null;
		var pos = latestPosition;
		var axes = plot.getAxes();
		if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
			pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
			return;
		}
		var i, j, dataset = plot.getData();
		for (i = 0; i < dataset.length; ++i) {
			var series = dataset[i];
			// Find the nearest points, x-wise
			for (j = 0; j < series.data.length; ++j) {
				if (series.data[j][0] > pos.x) {
					break;
				}
			}
		// Now Interpolate
		var y, p1 = series.data[j - 1],	p2 = series.data[j];
			if (p1 == null) {
				y = p2[1];
			} else if (p2 == null) {
				y = p1[1];
			} else {
				y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);
			}
			legends.eq(i).text(series.label.replace(/=.*/, "= " + bitProc(Math.round(y))));
		}
	}

	$("#box-"+period+"-content").bind("plothover",  function (event, pos, item) {
		latestPosition = pos;
		if (!updateLegendTimeout) {
			updateLegendTimeout = setTimeout(updateBWLegend, 50);
		}
	});
    
    



	function update() {


getRealData();
getRealTData();

// On Hover

var legends = $("#box-"+period+"-content .legendLabel");
	legends.each(function () {
		// fix the widths so they don't jump around
		$(this).css({'width': 80, 'float': 'left'});
	});
	var updateLegendTimeout = null;
	var latestPosition = null;
	function updateBWLegend() {
		updateLegendTimeout = null;
		var pos = latestPosition;
		var axes = plot.getAxes();
		if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
			pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
			return;
		}
		var i, j, dataset = plot.getData();
		for (i = 0; i < dataset.length; ++i) {
			var series = dataset[i];
			// Find the nearest points, x-wise
			for (j = 0; j < series.data.length; ++j) {
				if (series.data[j][0] > pos.x) {
					break;
				}
			}
		// Now Interpolate
		var y, p1 = series.data[j - 1],	p2 = series.data[j];
			if (p1 == null) {
				y = p2[1];
			} else if (p2 == null) {
				y = p1[1];
			} else {
				y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);
			}
			legends.eq(i).text(series.label.replace(/=.*/, "= " + bitProc(Math.round(y))));
		}
	}

	$("#box-"+period+"-content").bind("plothover",  function (event, pos, item) {
		latestPosition = pos;
		if (!updateLegendTimeout) {
			updateLegendTimeout = setTimeout(updateBWLegend, 50);
		}
	});
		plot.setData(
                        [
                            {data: realdata, label: ' RX = 0 '}, {data: realtdata, label: ' TX = 0 '}
                        ]
    );
		// Since the axes don't change, we don't need to call plot.setupGrid()
		plot.draw();
		setTimeout(update, updateInterval);
	}

	//update();
}






// Draw all Flot Charts
function DrawAllFlotCharts(){
<?php


foreach($periods as $period){
?>
	FlotGraph(<?php echo $interface_id?>, '<?php echo $period?>', <?php echo $result[$period]->num_rows?>, [<?php
                                                                //$c=0;
                                                                
                                                                $rowArray = array();
                                                                while($row = $result[$period]->fetch_object()){
                                                                    //$c++;
                                                                    $rowArray[] = "{'rx' : '".$row->rx."', 'tx' : '".$row->tx."', 'unixtimestamp' : ".$row->unixtimestamp."}";
                                                                }
                                                                echo implode(', ', $rowArray);
                                                                ?>]);
<?php
}
?>

}
$(document).ready(function() {
	// Load required Flot scripts and draw charts
	LoadFlotScripts(DrawAllFlotCharts);
	WinMove();
});
</script>
