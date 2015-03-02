<?php

$interface_id = intval($_GET['id']);

if($interface_id==0) die("haha!");

$interfaces = Database::$db->query("SELECT * FROM snmpinterfaces WHERE interface_local_id=$interface_id")->fetch_object();

$interface_id = $interfaces->interface_id;
$router_id = $interfaces->router_id;

?>


<?php

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
			<li><a href="#">Flot graphs</a></li>
		</ol>

	</div>
</div>


<?php


if($interfaces->active==1){

?>

<h1>Speed In And Speed Out</h1><hr />

<div class="row">

<?php

$rxtx_live_interface = Database::$db->query("SELECT * FROM snmptemp WHERE interface_id=".$interfaces->interface_id." AND router_id=".$interfaces->router_id)->fetch_object();

?>


	<div class="col-xs-12 col-sm-12" style="min-height: 100px;">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa  fa-area-chart"></i>
					<span>Realtime <span style="color:red; font-weight:600"><?php echo $rxtx_live_interface->interface_name?></span>  Graph <span class="interface_error" style="display:none; color:red; float:right; margin-right:100px"><i class="fa fa-bell"></i> ERROR: INTERFACE DOWN OR DISABLE</span></span>
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
				<div id="box-<?php echo $rxtx_live_interface->interface_local_id?>-content" style="min-height: 200px;"></div>
                
                                <div class="row" style="margin-top:20px">
                                
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Max <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="maxin_<?php echo $rxtx_live_interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Average <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="avgin_<?php echo $rxtx_live_interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>
                                                                        
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Current <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="curin_<?php echo $rxtx_live_interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Max <span style="color:#35D442">Out</span>: <span style="font-weight:600; color:#35D442" id="maxout_<?php echo $rxtx_live_interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>
                                                                        
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Average <span style="color:#35D442">Out</span>: <span style="font-weight:600; color:#35D442" id="avgout_<?php echo $rxtx_live_interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Current <span style="color:#35D442">Out</span>:  <span style="font-weight:600; color:#35D442" id="curout_<?php echo $rxtx_live_interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>
                                        
                                </div>
                
			</div>
		</div>
	</div>








<?php 



// For Hourly Report

$Query = "SELECT *, (`timestamp`) as unixtimestamp FROM snmplog WHERE `interface_id`=$interface_id AND `router_id`=$router_id ORDER BY timestamp DESC LIMIT 0, 30";

$result['hourly'] = Database::$db->query($Query);



// For Daily Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d %H' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 24";

$result['daily'] = Database::$db->query($Query);



// For Weekly Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 7";

$result['weekly'] = Database::$db->query($Query);



// For Monthly Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 30";

$result['monthly'] = Database::$db->query($Query);



// For Yearly Report


$Query = "  SELECT avg( rx ) AS rx, avg( tx ) AS tx, (`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m' ) AS newtime
                        FROM snmplog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY unixtimestamp DESC LIMIT 0, 12";

$result['yearly'] = Database::$db->query($Query);



?>






<?php

foreach($periods as $period){

?>
	<div class="col-xs-12 col-sm-12" style="min-height: 100px;">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa  fa-area-chart"></i>
					<span><?php echo ucfirst($period)?> <span style="color:red; font-weight:600"><?php echo $rxtx_live_interface->interface_name?></span>  Graph (Avg: <?php echo $avgs[$period]?>)  <span class="interface_error" style="display:none; color:red; float:right; margin-right:100px"><i class="fa fa-bell"></i> ERROR: INTERFACE DOWN OR DISABLE</span></span>
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


<script>


var periodXLabels = {
                hourly: 'Time (Minutes)', 
                daily: 'Time (Hours)', 
                weekly: 'Time (Days)', 
                monthly: 'Time (Days)', 
                yearly: 'Time (Months)'
            }


var periodMorrisXLabels = {
                hourly: 'Time (Minutes)', 
                daily: 'Time (Hours)', 
                weekly: 'Time (Days)', 
                monthly: 'Time (Days)', 
                yearly: 'Time (Months)'
            }








//
// Live Graph for max in and out
//
function FlotGraphLive(interface_id){
    
    
    
    
    
	// We use an inline data source in the example, usually data would
	// be fetched from a server
	var data = [],
	totalPoints = 180;	
	var datat = [],
	totalPoints = 180;	
var realdata = [];
var realtdata = [];



   function getRealData() {

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
                res.push([i, data[i]/1024])
                sum += data[i];
        }
        
            $('#curin_'+interface_id).html(bitProc(y))
            $('#maxin_'+interface_id).html(bitProc(Math.max.apply(Math, data)))
            $('#avgin_'+interface_id).html(bitProc((sum/data.length).toFixed(2)))
            
            realdata = res;
            
        });

	}



   function getRealTData() {

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
			res.push([i, datat[i]/1024])
            sum += datat[i];
		}
        $('#curout_'+interface_id).html(bitProc(y))
        $('#maxout_'+interface_id).html(bitProc(Math.max.apply(Math, datat)))
        $('#avgout_'+interface_id).html(bitProc((sum/datat.length).toFixed(2)))
        realtdata = res;
    
    });
	}



	function getRandomData() {
		if (data.length > 0)
			data = data.slice(1);
		// Do a random walk
		while (data.length < totalPoints) {
                var prev = data.length > 0 ? data[data.length - 1] : 0,
                y = prev + 0;

			if (y < 0) {
				y = 0;
			} else if (y > 100) {
				y = 100;
			}
			data.push(y);
		}
		// Zip the generated y values with the x values
		var res = [];
		for (var i = 0; i < data.length; ++i) {
			res.push([i, data[i]])
		}
		if (datat.length > 0)
			datat = datat.slice(1);
		// Do a random walk
		while (datat.length < totalPoints) {
                var prev = datat.length > 0 ? datat[datat.length - 1] : 0,
                y = prev + 0;

			if (y < 0) {
				y = 0;
			} else if (y > 100) {
				y = 100;
			}
			datat.push(y);
		}
		// Zip the generated y values with the x values
		var res = [];
		for (var i = 0; i < datat.length; ++i) {
			res.push([i, datat[i]])
		}
		return res;
	}



	var updateInterval = 1000;
	var plot = $.plot("#box-"+interface_id+"-content", 
                                [ 
                                    {data: getRandomData(), label: " RX = 0 kb "},
                                    {data: getRandomData(), label: " TX = 0 kb "}
                                ], {
		series: {
			shadowSize: 0,	// Drawing is faster without shadows
            lines: {
                fill: true
            }
		},
    grid: {
			hoverable: true,
            margin: {
                top: 8,
                bottom: 20,
                left: 20
            },
    },
    colors: ["#0979E8", "#35D442"], // rx, tx
		yaxis: {min: 0,	max: 0},
		xaxis: { show: true	}
	});





function update() {


getRealData();
getRealTData();

// On Hover


	var updateLegendTimeout = null;
	var latestPosition = null;
	function updateBWLegend() {

        var legends = $("#box-"+interface_id+"-content .legendLabel");
            legends.each(function () {
                // fix the widths so they don't jump around
                $(this).css({'width': 80, 'float': 'left'});
            });

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
            legends.eq(i).text(series.label.replace(/=.*/, "= " + y.toFixed(3) + ' Mb'));
		}
	}

	$("#box-"+interface_id+"-content").bind("plothover",  function (event, pos, item) {
		latestPosition = pos;
		if (!updateLegendTimeout) {
			updateLegendTimeout = setTimeout(updateBWLegend, 50);
		}
	});
		/*plot.setData(
                        [
                            {data: realdata, label: ' RX = 0 '}, {data: realtdata, label: ' TX = 0 '}
                        ]
    );*/
		// Since the axes don't change, we don't need to call plot.setupGrid()
		//plot.draw();
        if(Math.max.apply(Math, datat) > Math.max.apply(Math, data))
            var maxVal = Math.max.apply(Math, datat)
        else
            var maxVal = Math.max.apply(Math, data)

        plot = $.plot("#box-"+interface_id+"-content", 
                                [
                                    {data: realdata, label: ' RX = 0 Mb'}, {data: realtdata, label: ' TX = 0 Mb'}
                                ], {
            series: {
                shadowSize: 0,  // Drawing is faster without shadows
                lines: {
                    fill: true
                }
            },
        grid: {
                hoverable: true,
                margin: {
                    top: 8,
                    bottom: 20,
                    left: 20
                },
        },
        colors: ["#0979E8", "#35D442"], // rx, tx
            yaxis: {min: 0, max: (maxVal + (maxVal * 20 / 100))/1024},
            xaxis: { show: true }
        });

    // Create the demo X and Y axis labels

    var xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
        .text("Time (Secounds)")
        .appendTo("#box-"+interface_id+"-content");

    var yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
        .text("Speed (Mbps)")
        .appendTo("#box-"+interface_id+"-content");

    //yaxisLabel.css("margin-top", yaxisLabel.width() / 2 - 20);



		setTimeout(update, updateInterval);
	}

	update();
}










//
// Static Graph for tick diagrams
//
function FlotGraph(interface_id, period, num_rows, rows, maxR, maxT, currR, currT, sumR, sumT){
    
    

    
	// We use an inline data source in the example, usually data would
	// be fetched from a server

var data = [];	
var datat = [];
var realdata = [];
var realtdata = [];



function getRandomData(type) {
    
    if(type=='rx'){
		if (data.length > 0)
			data = data.slice(1);
    
        $.each(rows, function(index, element){
            data.push([(element.unixtimestamp*1000), Math.round(element.rx)/1024])
        })      
    }

    if(type=='tx'){
		if (datat.length > 0)
			datat = datat.slice(1);

        $.each(rows, function(index, element){
                datat.push([(element.unixtimestamp*1000), Math.round(element.tx)/1024])
        })    
    
    }


		// Zip the generated y values with the x values
		var res = [];
        var sum = 0;
        if(type=='rx'){
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]/1024])      
                sum += data[i]
                
            }
        }else{
            for (var i = 0; i < datat.length; ++i) {
                res.push([i, datat[i]/1024])
                sum += datat[i]
                
            }
        }

        $('#curin_'+period).html(bitProc(currR))
        $('#maxin_'+period).html(bitProc(maxR))
        $('#avgin_'+period).html(bitProc((sumR/num_rows).toFixed(2)))
        $('#curout_'+period).html(bitProc(currT))
        $('#maxout_'+period).html(bitProc(maxT))
        $('#avgout_'+period).html(bitProc((sumT/num_rows).toFixed(2)))
    
    
        if(type=='rx')
            return data
        else
            return datat
		return res;
	}






var dayOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thr", "Fri", "Sat"];

switch(period){

        case 'yearly':
            var xaxis =  { 
                        mode: "time",
                        timeformat: "%Y/%m",
                        timezone: "browser"
            }
        break;

        case 'monthly':
            var xaxis =  { 
                        mode: "time",     
                        timeformat: "%m-%d",
                        labelAngle: -90,
                        timezone: "browser"
            }
        break;

        case 'weekly':
            var xaxis =  { 
                        mode: "time",       
                        tickFormatter: function (val, axis) {           
                            return dayOfWeek[new Date(val).getDay()];
                        },
                        tickSize: [1, "day"],
                        timezone: "browser"
            }
        break;

        case 'daily':
            var xaxis =  { 
                        mode: "time",
                        timeformat: "%H",
                        timezone: "browser"
            }
        break;

        case 'hourly':
            var xaxis =  { 
                        mode: "time",
                        timeformat: "%H:%M",
                        timezone: "browser"
            }
        break;
        


}
    
    var dataR = getRandomData('rx')
    var dataT = getRandomData('tx')

        if(maxT > maxR)
            var maxVal = maxT
        else
            var maxVal = maxR

	var updateInterval = 1000;
	var plot = $.plot("#box-"+period+"-content", 
                                [ 
                                    {data: dataR, label: " RX = 0 Mb "},
                                    {data: dataT, label: " TX = 0 Mb "}
                                ], {
		series: {
			shadowSize: 0,	// Drawing is faster without shadows
            lines: {
                fill: true
            }
		},
        grid: {
                hoverable: true,
                margin: {
                    top: 8,
                    bottom: 20,
                    left: 20
                },
        },
    colors: ["#0979E8", "#35D442"], // rx, tx
    yaxis: {min: 0,	max: (maxVal + (maxVal * 20 / 100))/1024},
    xaxis: xaxis,

    
	});


$("#box-"+period+"-content div.xAxis div.tickLabel").css({
    'margin-top' : '20px',
    'font-size' : '9.5pt',
    'transform': "rotate(-70deg)",
    '-ms-transform': "rotate(-70deg)",
    '-moz-transform': "rotate(-70deg)",
    '-webkit-transform': "rotate(-70deg)",
    '-o-transform': "rotate(-70deg)",
})

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
			legends.eq(i).text(series.label.replace(/=.*/, "= " + y.toFixed(3) + ' Mb'));
		}
	}

    var yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
        .text("Speed (Mbps)")
        .appendTo("#box-"+period+"-content");

    var xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
        .text(periodXLabels[period])
        .appendTo("#box-"+period+"-content");

	$("#box-"+period+"-content").bind("plothover",  function (event, pos, item) {
		latestPosition = pos;
		if (!updateLegendTimeout) {
			updateLegendTimeout = setTimeout(updateBWLegend, 50);
		}
	});




    // update transmit hourly chart every 2 minutes
    function updateHourly(){
        $.ajax({ 
            url: '<?php echo SITE_URL?>subviews/graph/interface_refreshstatics.php',
            dataType: 'json',
            data: {type: 'rxtx', period: 'hourly', 'router_id': <?php echo $router_id?>, 'interface_id': <?php echo $interface_id?>}
        })
        .done(function(ret){ // refreshing hourly chart every 2 minute
            console.log(ret)
            FlotGraph(<?php echo $interfaces->interface_id?>, 'hourly', ret.num_rows, ret.data, ret.maxR, ret.maxT, ret.currR, ret.currT, sumR, sumT);
        })
        setTimeout(updateHourly, 120000);
    }
    setTimeout(updateHourly, 120000);



    // update transmit hourly chart every 2 minutes
    function updateDaily(){
        $.ajax({ 
            url: '<?php echo SITE_URL?>subviews/graph/interface_refreshstatics.php',
            dataType: 'json',
            data: {type: 'rxtx', period: 'daily', 'router_id': <?php echo $router_id?>, 'interface_id': <?php echo $interface_id?>}
        })
        .done(function(ret){ // refreshing hourly chart every 2 minute
            console.log(ret)
            FlotGraph(<?php echo $interfaces->interface_id?>, 'daily', ret.num_rows, ret.data, ret.maxR, ret.maxT, ret.currR, ret.currT, sumR, sumT);
        })
        setTimeout(updateDaily, 120000);
    }
    setTimeout(updateDaily, 120000);



    // update transmit hourly chart every 2 minutes
    function updateWeekly(){
        $.ajax({ 
            url: '<?php echo SITE_URL?>subviews/graph/interface_refreshstatics.php',
            dataType: 'json',
            data: {type: 'rxtx', period: 'weekly', 'router_id': <?php echo $router_id?>, 'interface_id': <?php echo $interface_id?>}
        })
        .done(function(ret){ // refreshing hourly chart every 2 minute
            console.log(ret)
            FlotGraph(<?php echo $interfaces->interface_id?>, 'weekly', ret.num_rows, ret.data, ret.maxR, ret.maxT, ret.currR, ret.currT, sumR, sumT);
        })
        setTimeout(updateWeekly, 120000);
    }
    setTimeout(updateWeekly, 120000);




    // update transmit hourly chart every 2 minutes
    function updateMonthly(){
        $.ajax({ 
            url: '<?php echo SITE_URL?>subviews/graph/interface_refreshstatics.php',
            dataType: 'json',
            data: {type: 'rxtx', period: 'monthly', 'router_id': <?php echo $router_id?>, 'interface_id': <?php echo $interface_id?>}
        })
        .done(function(ret){ // refreshing hourly chart every 2 minute
            console.log(ret)
            FlotGraph(<?php echo $interfaces->interface_id?>, 'monthly', ret.num_rows, ret.data, ret.maxR, ret.maxT, ret.currR, ret.currT, sumR, sumT);
        })
        setTimeout(updateMonthly, 120000);
    }
    setTimeout(updateMonthly, 120000);




    // update transmit hourly chart every 2 minutes
    function updateYearly(){
        $.ajax({ 
            url: '<?php echo SITE_URL?>subviews/graph/interface_refreshstatics.php',
            dataType: 'json',
            data: {type: 'rxtx', period: 'yearly', 'router_id': <?php echo $router_id?>, 'interface_id': <?php echo $interface_id?>}
        })
        .done(function(ret){ // refreshing hourly chart every 2 minute
            console.log(ret)
            FlotGraph(<?php echo $interfaces->interface_id?>, 'yearly', ret.num_rows, ret.data, ret.maxR, ret.maxT, ret.currR, ret.currT, sumR, sumT);
        })
        setTimeout(updateYearly, 120000);
    }
    setTimeout(updateYearly, 120000);


    
}





// Draw all Flot Charts
function DrawAllFlotCharts(){

	FlotGraphLive(<?php echo $rxtx_live_interface->interface_local_id?>);
<?php


foreach($periods as $period){
?>
	FlotGraph(<?php echo $interfaces->interface_id?>, '<?php echo $period?>', <?php echo $result[$period]->num_rows?>, [<?php
                                                                //$c=0;
                                                                
                                                                $rowArray = array();
                                                                $rowR = array();
                                                                $rowT = array();
                                                                while($row = $result[$period]->fetch_object()){
                                                                    //$c++;
                                                                    $rowArray[] = "{'rx' : ".$row->rx.", 'tx' : ".$row->tx.", 'unixtimestamp' : ".strtotime($row->unixtimestamp)."}";
                                                                    $rowR[] = $row->rx;
                                                                    $rowT[] = $row->tx;
                                                                }
                                                                $rowArray = array_reverse($rowArray);
                                                                echo implode(', ', $rowArray);
                                                                ?>], 
                                                                <?php echo max($rowR)?>, 
                                                                <?php echo max($rowT)?>, 
                                                                <?php echo $rowR[0]?>, 
                                                                <?php echo $rowT[0]?>,
                                                                <?php echo array_sum($rowR)?>, 
                                                                <?php echo array_sum($rowT)?> );
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



</div>


<?php

}


?>




<!-- Show bytes in and out if checked. -->



<?php


if($interfaces->bytesactive==1){


// For Hourly Report

$Query = "SELECT *, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp FROM snmpbytelog WHERE `interface_id`=$interface_id AND `router_id`=$router_id ORDER BY timestamp DESC LIMIT 0, 30";

$Bytesresult['hourly'] = Database::$db->query($Query);



// For Daily Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d %H' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 24";

$Bytesresult['daily'] = Database::$db->query($Query);



// For Weekly Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 7";

$Bytesresult['weekly'] = Database::$db->query($Query);



// For Monthly Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m-%d' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 30";

$Bytesresult['monthly'] = Database::$db->query($Query);



// For Yearly Report


$Query = "  SELECT sum( `in` ) AS `in`, sum( `out` ) AS `out`, UNIX_TIMESTAMP(`timestamp`) as unixtimestamp, FROM_UNIXTIME( UNIX_TIMESTAMP( `timestamp` ) , '%Y-%m' ) AS newtime
                        FROM snmpbytelog WHERE `interface_id`=$interface_id  AND `router_id`=$router_id
                        GROUP BY newtime
                        ORDER BY newtime DESC LIMIT 0, 12";

$Bytesresult['yearly'] = Database::$db->query($Query);


?>

<h1>Bytes In And Bytes Out</h1><hr />

<div class="row">

<?php
foreach($avgs as $period => $time){
?>

	<div class="col-xs-12 col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-bar-chart-o"></i>
					<span><?php echo ucfirst($period) ?> <span style="color:red; font-weight:600"><?php echo $rxtx_live_interface->interface_name?></span> In/Out Chart (Sum: <?php echo $time?>)  <span class="interface_error" style="display:none; color:red; float:right; margin-right:100px"><i class="fa fa-bell"></i> ERROR: INTERFACE DOWN OR DISABLE</span></span>
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





<script>



function MorrisChart(day_data, element, keys, labels){

	Morris.Bar({
		element: 'morris-chart-'+element,
		data: day_data,
		xkey: 'period',
     barColors: ['#0979E8', '#35D442', '#408080'],
		ykeys: keys,
		labels: labels,
		xLabelAngle: 60
	});




}



// Draw all test Morris Charts
function DrawAllMorrisCharts(){
    
    <?php
    
    foreach($periods as $period){
        ?>
        MorrisChart([<?php
                                            $records = array();
                                            while($row = $Bytesresult[$period]->fetch_object()){
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
                                                $records[] = "{'period' : '$jsperiod', 'in' : ".round($row->in/8192, 2).", 'out' : ".round($row->out/8192, 2).", 'sum' : ".round(($row->in+$row->out)/8192, 2)."}";
                                            }
                                            $records = array_reverse($records, true);
                                            echo implode(', ', $records);
                                            ?>], '<?php echo $period?>', ['in', 'out', 'sum'], ['In', 'Out', 'Sum']);



    var yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
        .text("Bytes (MB)")
        .appendTo("#morris-chart-<?php echo $period?>");

    var xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
        .text(periodMorrisXLabels['<?php echo $period?>'])
        .appendTo("#morris-chart-<?php echo $period?>");
    
    $("#morris-chart-<?php echo $period?> svg").css({'margin-left' : '20px'});

    <?php
    }
    
    ?>
    
    $("#morris-chart-daily svg").css({'margin-top' : '-20px'});
    //MorrisChart( ['in', 'out', 'sum'], ['In', 'Out', 'Sum'])
    
}



$(document).ready(function() {
	LoadMorrisScripts(DrawAllMorrisCharts);
	WinMove();
});


</script>


<?php

}

?>

<script>


var interfaceStatus = 2;

function interface_error_checker(){


    if(interfaceStatus!=2){
        $('.interface_error').toggle()
    }else{
        $('.interface_error').hide()
    }


        $.ajax({
            url: site_url+"snmprouterval.php",
            data : {
                        'interface_id' : <?php echo $interface_id?>,
                        'router_id' : <?php echo $router_id?>,
                        'type' : 'interfaceStatus'
            }
            //async:false
        }).done(function(ret) {
            interfaceStatus = ret    
        //alert(ret)   
        });
    setTimeout(interface_error_checker, 200)

}

interface_error_checker();


</script>