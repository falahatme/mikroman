<div class="row">
    <div id="breadcrumb" class="col-xs-12">
        <a href="#" class="show-sidebar">
            <i class="fa fa-bars"></i>
        </a>
        <ol class="breadcrumb pull-left">
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">WebProxy Logger</a></li>
            <li><a href="#">Report</a></li>
        </ol>
    </div>
</div>


<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <div class="box-header">
                <div class="box-name">
                    <i class="fa fa-search"></i>
                    <span>WebProxy Log</span>
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
                <h4 class="page-header">Device Detail</h4>

                <form class="form-horizontal" action="" method="post" role="form">

                    <input type="hidden" name="com" value="check" />

                    <div class="form-group has-error">
                        <label class="col-sm-2 control-label">ID</label>
                        <div class="col-sm-4">
                            <input type="text" name="id" class="form-control" placeholder="MAC or IP" data-toggle="tooltip" data-placement="bottom" title="Tooltip for name">
                        </div>
                        <label class="col-sm-2 control-label">Username</label>
                        <div class="col-sm-4">
                            <input type="text" name="username" class="form-control" placeholder="username" data-toggle="tooltip" data-placement="bottom" title="Tooltip for name">
                        </div>
                    </div>


                <h4 class="page-header">Log Detail</h4>


                    <div class="form-group has-error has-feedback">
                        <label class="col-sm-2 control-label">Start</label>
                        <div class="col-sm-2">
                            <input type="text" id="input_date_start" name="start[date]" class="form-control" required="required" placeholder="Date">
                            <span class="fa fa-calendar txt-danger form-control-feedback"></span>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" id="input_time_start" name="start[time]" class="form-control" required="required" placeholder="Time">
                            <span class="fa fa-clock-o txt-danger form-control-feedback"></span>
                        </div>

                        <label class="col-sm-2 control-label">End</label>
                        <div class="col-sm-2">
                            <input type="text" id="input_date_end" name="end[date]" class="form-control" placeholder="Date">
                            <span class="fa fa-calendar txt-danger form-control-feedback"></span>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" id="input_time_end" name="end[time]" class="form-control" placeholder="Time">
                            <span class="fa fa-clock-o txt-danger form-control-feedback"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Destination</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="host" placeholder="www.example.com or Server IP" data-toggle="tooltip" data-placement="bottom" title="www.example.com">
                        </div>

                        <label class="col-sm-2 control-label">Referer</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="referer" placeholder="www.example.com">
                        </div>
                    </div>




                    <div class="form-group has-feedback">
                        <label class="col-sm-2 control-label">Content Type</label>
                        <div class="col-sm-4">
                            <select id="s2_with_tag" name="content_type" class="populate placeholder">
                                <option value="all">All</option>
                                <option value="application">Application</option>
                                <option value="audio">Audio</option>
                                <option value="chemical">Chemical</option>
                                <option value="image">Image</option>
                                <option value="model">Model</option>
                                <option value="text">Text</option>
                                <option value="video">Video</option>
                            </select>
                        </div>


                        <label class="col-sm-2 control-label">Access</label>
                        <div class="col-sm-4">
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="action" value="allow"> Allow
                                    <i class="fa fa-circle-o"></i>
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="action" value="deny"> Deny
                                    <i class="fa fa-circle-o"></i>
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="action" checked value="both"> Both
                                    <i class="fa fa-circle-o"></i>
                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="clearfix"></div>
                    <div class="form-group">

                        <label class="col-sm-2 control-label">Limit</label>
                        <div class="col-sm-2">
                            <select id="s2_with_tag" name="limit" class="populate placeholder">
                                <option value="50">50</option>
                                <option selected value="100">100</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                                <option value="nolimit">All</option>
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary btn-label-left">
                                <span><i class="fa fa-clock-o"></i></span>
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<?php

@ $com = functions::match('username', $_POST['com']);
if(isset($com) and $com=='check'){


    $query = "SELECT *,'log' FROM log WHERE ";
    $conditions = array();
    $dhcpQuery = "SELECT * FROM dhcp_server,brands WHERE ";
    $dhcpConditions = array();



@$id = Database::$db->real_escape_string($_POST['id']);
if(strlen($id)>0){
    switch(detectId($id)){

        case 'mac':
            $conditions[] = "ip IN (SELECT client_id as ip FROM dhcp_server WHERE mac = '$id')";
            $dhcpConditions[] = "mac='$id'";
            break;

        case 'ip':
            $conditions[] = "ip='$id'";
            $dhcpConditions[] = "client_id='$id'";
            break;

    }
}



    @$start = $_POST['start'];
    @$start_date = Database::$db->real_escape_string($start['date']);
    @$start_time = Database::$db->real_escape_string($start['time']);
    @list($y, $m, $d) = explode('-', $start_date);
    @$start_date = implode('-', $calendar->jalali_to_gregorian($y, $m, $d));

    if(strlen($start_date)>0 and strlen($start_time)>0){
        $conditions[] = " timestamp >= '{$start_date} {$start_time}:00'";
        $dhcpConditions[] = " timestamp >= '{$start_date} {$start_time}:00'";
    }


    @$end = $_POST['end'];
    @$end_date = Database::$db->real_escape_string($end['date']);
    @$end_time = Database::$db->real_escape_string($end['time']);
    @list($y, $m, $d) = explode('-', $end_date);
    @$end_date = implode('-', $calendar->jalali_to_gregorian($y, $m, $d));

    if(strlen($end_date)>0 and strlen($end_time)>0){
        $conditions[] = " timestamp <= '{$end_date} {$end_time}:59'";
        $dhcpConditions[] = " timestamp <= '{$end_date} {$end_time}:59'";
    }else{
        $end_date = date('Y-m-d');
        $end_time = date('H:i');
    }

    @ $host = Database::$db->real_escape_string($_POST['host']);
    if(strlen($host)>0){
       // if(strlen(filter_var($host, FILTER_VALIDATE_IP))<1)
            $conditions[] = "host LIKE '%$host%'";
    }

    @ $referer = Database::$db->real_escape_string($_POST['referer']);
    if(strlen($referer)>0){
        $conditions[] = "host LIKE '%$referer%'";
    }

    @$content_type = Database::$db->real_escape_string($_POST['content_type']);
    switch($content_type){

        case 'all':
            break;
        default:
            $conditions[] = "content_type LIKE '%{$content_type}/%'";
            break;

    }

    @$action = Database::$db->real_escape_string($_POST['action']);
    switch($action){

        case 'both':
            break;
        default:
            $conditions[] = "action='$action'";
            break;

    }


    $query .= implode(' AND ', $conditions);

    $query .= " GROUP BY request";



    $dhcpConditions[] = " SUBSTRING(dhcp_server.mac, 1, 8)=brands.hex";

    $dhcpConditions[] = " type='assign'";

    $dhcpQuery .= implode(' AND ', $dhcpConditions);

    $dhcpQuery .= " GROUP BY mac,client_id";

    $dhcpQuery .= " ORDER BY dhcp_server.dhcp_server_id DESC";




if($content_type=="all"){
    // FIREWALL PART OF RESULTS
    $query .= " UNION SELECT firewall.*,organization,country,telephone,end,'firewall' FROM firewall LEFT JOIN whois ON(whois.ip=firewall.target_ip) WHERE ";
    $query .= " ((start BETWEEN '{$start_date} {$start_time}:00' AND '{$end_date} {$end_time}:59')
                OR
                (end BETWEEN '{$start_date} {$start_time}:00' AND '{$end_date} {$end_time}:59'))";
if(strlen($id)>0){
        switch(detectId($id)){
            case 'mac':
                $query .= " AND firewall.mac='$id' ";
                break;
            case 'ip':
                $query .= " AND firewall.ip='$id' ";
                break;
    }
}
    if(strlen($host)>0){
            $query .= " AND firewall.target_ip='$host' ";
    }
     switch($action){

        case 'both':
            break;
        case 'deny':
            $query .= " AND firewall.start='$action' ";
            break;
        default:
            break;

}
}


    $query .= " ORDER BY timestamp DESC, content_length DESC";

    @ $limit = functions::match('username', $_POST['limit']);
    if($limit != "nolimit"){
        $query .= " LIMIT 0, ".intval($_POST['limit']);
        $dhcpQuery .= " LIMIT 0, ".intval($_POST['limit']);
    }


    //echo $query;
    //echo $dhcpQuery;


    $logs = Database::$db->query($query);
    $dhcpLogs = database::$db->query($dhcpQuery);


    $cidr = array(
        '128.0.0.0' => '/1',
        '192.0.0.0' => '/2',
        '224.0.0.0' => '/3',
        '240.0.0.0' => '/4',
        '248.0.0.0' => '/5',
        '252.0.0.0' => '/6',
        '254.0.0.0' => '/7',
        '255.0.0.0' => '/8',
        '255.128.0.0' => '/9',
        '255.192.0.0' => '/10',
        '255.224.0.0' => '/11',
        '255.240.0.0' => '/12',
        '255.248.0.0' => '/13',
        '255.252.0.0' => '/14',
        '255.254.0.0' => '/15',
        '255.255.0.0' => '/16',
        '255.255.128.0' => '/17',
        '255.255.192.0' => '/18',
        '255.255.224.0' => '/19',
        '255.255.240.0' => '/20',
        '255.255.248.0' => '/21',
        '255.255.252.0' => '/22',
        '255.255.254.0' => '/23',
        '255.255.255.0' => '/24',
        '255.255.255.128' => '/25',
        '255.255.255.192' => '/26',
        '255.255.255.224' => '/27',
        '255.255.255.240' => '/28',
        '255.255.255.248' => '/29',
        '255.255.255.252' => '/30',
        '255.255.255.254' => '/31',
        '255.255.255.255' => '/32',
    );



    ?>


<script>

    $('title').html('WebProxy Report <?php echo $id?>');

</script>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-name">
                        <i class="fa fa-linux"></i>
                        <span>Device Log Result</span>
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
                <div class="box-content no-padding table-responsive">
                    <table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2">
                        <thead>
                        <tr>
                            <th>Index<label><input type="text" name="search_index" value="Index" class="search_init" /></label></th>
                            <th>Date<label><input type="text" name="search_date" value="Date" class="search_init" /></label></th>
                            <th>Time<label><input type="text" name="search_time" value="Time" class="search_init" /></label></th>
                            <th>IP<label><input type="text" name="search_ip" value="IP" class="search_init" /></label></th>
                            <th>MAC<label><input type="text" name="search_ip" value="MAC" class="search_init" /></label></th>
                            <th>Host<label><input type="text" name="search_host" value="Host" class="search_init" /></label></th>
                            <th>Brand<label><input type="text" name="search_access" value="Brand" class="search_init" /></label></th>
                            <th>Server IP<label><input type="text" name="search_os" value="Server IP" class="search_init" /></label></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 0;
                        while($row = $dhcpLogs->fetch_object()) {
                            $index++;
                            list($dhcpDate, $dhcpTime) = explode(' ', $row->timestamp);
                            ?>
                            <tr>
                                <td><?php echo $index?></td>
                                <td>
                                    <?php
                                    list($y, $m, $d) = explode('-', $dhcpDate);
                                    $dhcpDate = implode('-', $calendar->gregorian_to_jalali($y, $m, $d));
                                    echo $dhcpDate;
                                    ?>
                                </td>
                                <td><?php echo $dhcpTime?></td>
                                <td><?php echo $row->client_id.$cidr[$row->subnet_mask]?></td>
                                <td><?php echo $row->mac?></td>
                                <td><?php echo $row->host_name?></td>
                                <td><?php echo $row->company?></td>
                                <td><?php echo $row->server_id?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-name">
                        <i class="fa fa-linux"></i>
                        <span>Log result</span>
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
                <div class="box-content no-padding table-responsive">
                    <table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-3">
                        <thead>
                        <tr>
                            <th>Index<label><input type="text" name="search_index" value="Index" class="search_init" /></label></th>
                            <th>Service<label><input type="text" name="search_date" value="Type" class="search_init" /></label></th>
                            <th>Date<label><input type="text" name="search_date" value="Date" class="search_init" /></label></th>
                            <th>Time<label><input type="text" name="search_time" value="Time" class="search_init" /></label></th>
                            <th>IP<label><input type="text" name="search_ip" value="IP" class="search_init" /></label></th>
                            <th>Host , Organization<label><input type="text" name="search_host" value="Host" class="search_init" /></label></th>
                            <th><span style="color:green">Request</span> , <span style="color:red">Target IP</span><label><input type="text" name="search_request" value="Request" class="search_init" /></label></th>
                            <th>Referer , Public IP<label><input type="text" name="search_referer" value="Referer" class="search_init" /></label></th>
                            <th>Action<label><input type="text" name="search_access" value="Access" class="search_init" /></label></th>
                            <th>Browser<label><input type="text" name="search_browser" value="Browser" class="search_init" /></label></th>
                            <th>OS<label><input type="text" name="search_os" value="OS" class="search_init" /></label></th>
                            <th>Type<label><input type="text" name="search_type" value="Type" class="search_init" /></label></th>
                            <th>Size<label><input type="text" name="search_length" value="Length" class="search_init" /></label></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 0;
                        while($row = $logs->fetch_object()) {
                            $index++;


                            if($row->log == "log"){


                                ?>
                                <tr>
                                    <td><?php echo $index?></td>
                                    <td><span style="color:green">HTTP</span></td>
                                    <td>
                                        <?php
                                        list($y, $m, $d) = explode('/', $row->date);
                                        $ddate = implode('-', $calendar->gregorian_to_jalali($y, $m, $d));
                                        echo $ddate;
                                        ?>
                                    </td>
                                    <td><?php echo $row->time?></td>
                                    <td><?php echo $row->ip?></td>
                                    <td><?php echo $row->host?></td>
                                    <td><a style="color:green !important" href="<?php echo $row->request?>" target="_blank"><?php echo chunk_split($row->request, 30, '<br />')?></a></td>
                                    <td><a href="<?php echo $row->referer?>" target="_blank"><?php echo chunk_split($row->referer, 20, '<br />')?></a></td>
                                    <td><?php echo $row->action?></td>
                                    <td><?php echo $row->browser?></td>
                                    <td><?php echo $row->platform?></td>
                                    <td><?php echo $row->content_type?></td>
                                    <td><?php echo showSize($row->content_length)?></td>
                                </tr>
                            <?php


                            }elseif($row->log == "firewall"){

                                $start = explode(' ', $row->action);
                                $end = explode(' ', $row->browser);

                                ?>
                                <tr>
                                    <td><?php echo $index?></td>
                                    <td><span style="color:red">HTTPS</span></td>
                                    <td>
                                        <?php echo $start[0]?> -> <?php echo $end[0]?>
                                    </td>
                                    <td>
                                        <?php echo $start[1]?> -> <?php echo $end[1]?>
                                    </td>
                                    <td><?php echo $row->time.':'.$row->ip?></td>
                                    <td><?php echo @$row->platform?></td>
                                    <td><span style="color:red"><?php echo $row->request.':'.$row->referer?></span></td>
                                    <td><?php echo @$row->host?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php

                            }


                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




<?php

}

?>






<script type="text/javascript">
    // Run Datables plugin and create 3 variants of settings
    function AllTables(){
        //TestTable1();
        TestTable2();
        TestTable3();
        LoadSelect2Script(MakeSelect2);
    }
    function MakeSelect2(){
        $('select').select2();
        $('.dataTables_filter').each(function(){
            $(this).find('label input[type=text]').attr('placeholder', 'Search');
        });
    }
    $(document).ready(function() {
        // Add drag-n-drop feature to boxes
        // Load Datatables and run plugin on tables
        LoadDataTablesScripts(AllTables);
        // Create Wysiwig editor for textare
        TinyMCEStart('#wysiwig_simple', null);
        TinyMCEStart('#wysiwig_full', 'extreme');
        // Add slider for change test input length
        FormLayoutExampleInputLength($( ".slider-style" ));
        // Initialize datepicker
        $('#input_date_start,#input_date_end').datepicker({dateFormat: 'yy-mm-dd',setDate: new Date()});
        // Load Timepicker plugin
        LoadTimePickerScript(DemoTimePicker);
        // Add tooltip to form-controls
        $('.form-control').tooltip();
        LoadSelect2Script(DemoSelect2);
        // Load example of form validation
        LoadBootstrapValidatorScript(DemoFormValidator);
        // Add Drag-n-Drop feature
        WinMove();
    });
    // Run Select2 plugin on elements
    function DemoSelect2(){
        $('#s2_with_tag').select2({placeholder: "Select OS"});
        $('#s2_country').select2();
    }
    // Run timepicker
    function DemoTimePicker(){
        $('#input_time_start,#input_time_end').timepicker({setDate: new Date()});
    }
</script>
