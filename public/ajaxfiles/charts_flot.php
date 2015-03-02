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





<div class="row">


<?php
$interfaces = table('snmptemp')->where(array('active=1'))->run();
?>


<?php
while($interface = $interfaces->fetch_object()){
?>
	<div class="col-xs-12 col-sm-6" style="min-height: 100px;">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa  fa-area-chart"></i>
					<span>Realtime <span style="color:red; font-weight:600"><?php echo $interface->interface_name?></span>  Graph</span>
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
				<div id="box-<?php echo $interface->interface_local_id?>-content" style="min-height: 200px;"></div>
                
                                <div class="row" style="margin-top:20px">
                                
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Max <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="maxin_<?php echo $interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Average <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="avgin_<?php echo $interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>
                                                                        
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Current <span style="color:#0979E8">In</span>: <span style="font-weight:600; color:#0979E8" id="curin_<?php echo $interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Max <span style="color:#35D442">Out</span>: <span style="font-weight:600; color:#35D442" id="maxout_<?php echo $interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>
                                                                        
                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Average <span style="color:#35D442">Out</span>: <span style="font-weight:600; color:#35D442" id="avgout_<?php echo $interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>                                

                                        <div class="col-xs-12 col-sm-4">
                                                <div class="box" style="border:none !important">
                                                Current <span style="color:#35D442">Out</span>:  <span style="font-weight:600; color:#35D442" id="curout_<?php echo $interface->interface_local_id?>">0</span>
                                                </div>
                                        </div>
                                        
                                </div>
                
			</div>
		</div>
	</div>
<?php
}
?>
    
    
    
<div class="row">
	<div class="col-xs-12">
		<div class="box boxtest">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-search"></i>
					<span>Thresholds</span>
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
				<div id="box-three-content" style="min-height: 200px;"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-search"></i>
					<span>Series Types</span>
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
				<div id="box-four-content" style="min-height: 400px;"></div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">






//
// Graph1 created in element with id = box-one-content
//
function FlotGraph(interface_id){
    
    
    
    
    
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
                res.push([i, data[i]])
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
			res.push([i, datat[i]])
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
			hoverable: true
    },
    colors: ["#0979E8", "#35D442"], // rx, tx
		yaxis: {min: 0,	max: 3000},
		xaxis: { show: true	}
	});






	function update() {


getRealData();
getRealTData();

// On Hover

var legends = $("#box-"+interface_id+"-content .legendLabel");
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

	$("#box-"+interface_id+"-content").bind("plothover",  function (event, pos, item) {
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

	update();
}






// Draw all Flot Charts
function DrawAllFlotCharts(){
<?php

$interfaces = table('snmptemp')->where(array('active=1'))->run();

while($interface = $interfaces->fetch_object()){
?>
	FlotGraph(<?php echo $interface->interface_local_id?>);
<?php
}
?>	//FlotGraph1();
	FlotGraph2();
	FlotGraph3();
	FlotGraph4();

}
$(document).ready(function() {
	// Load required Flot scripts and draw charts
	LoadFlotScripts(DrawAllFlotCharts);
	WinMove();
});
</script>
