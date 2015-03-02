<div class="row">
    <div id="breadcrumb" class="col-xs-12">
        <a href="#" class="show-sidebar">
            <i class="fa fa-bars"></i>
        </a>
        <ol class="breadcrumb pull-left">
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Routers</a></li>
            <li><a href="#">Routers</a></li>
        </ol>
    </div>
</div>


<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="box">
            <div class="box-header">
                <div class="box-name">
                    <i class="fa fa-search"></i>
                    <span>Add New Router</span>
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
                <h4 class="page-header">Step 1:</h4>

                <form class="form-horizontal" action="<?php echo SITE_URL?>subviews/routers/router_step2.php" method="post" role="form" id="router_step1">

                    <input type="hidden" name="com" value="check" />

                    <div class="form-group has-error">
                        <label class="col-sm-3 control-label">IP Address</label>
                        <div class="col-sm-6">
                            <input type="text" name="router_ip" class="form-control" placeholder="Router IP Address" required="required" data-toggle="tooltip" data-placement="bottom">
                        </div>
                    </div>

                    <div class="form-group has-error">
                        <label class="col-sm-3 control-label">Username</label>
                        <div class="col-sm-6">
                            <input type="text" name="router_user" class="form-control" placeholder="Router Username" required="required" data-toggle="tooltip" data-placement="bottom">
                        </div>
                    </div>

                    <div class="form-group has-error">
                        <label class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-6">
                            <input type="password" name="router_pass" class="form-control" placeholder="1234" required="required" data-toggle="tooltip" data-placement="bottom">
                        </div>
                    </div>

                    <div class="form-group has-error">
                        <label class="col-sm-3 control-label">SSH Port</label>
                        <div class="col-sm-6">
                            <input type="text" name="router_ssh_port" class="form-control" placeholder="Router SSH Port" required="required" value="22" data-toggle="tooltip" data-placement="bottom" title="Default port is: 22">
                        </div>
                    </div>




                    <div class="form-group has-error">
                    <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-6">
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






    <div class="col-xs-12 col-sm-6">
        <div class="box">
            <div class="box-header">
                <div class="box-name">
                    <i class="fa fa-search"></i>
                    <span>Add New Router</span>
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
            <div class="box-content" id="step2_result">
            
            <h1 style="margin:auto; text-align:center">Submit Step 1 First.</h1>

            </div>
        </div>
    </div>




























<?php 
$query = "SELECT * FROM routers";
$routers = Database::$db->query($query);
?>


    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-name">
                        <i class="fa fa-linux"></i>
                        <span>Added Routers</span>
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
                            <th style="text-align:center">Index<label><input type="text" name="search_index" value="Index" class="search_init" /></label></th>
                            <th style="text-align:center">Router IP<label><input type="text" name="search_date" value="Router IP" class="search_init" /></label></th>
                           <th style="text-align:center">Identity<label><input type="text" name="search_ip" value="Identity" class="search_init" /></label></th>
                            <th style="text-align:center">Location<label><input type="text" name="search_host" value="Location" class="search_init" /></label></th>
                             <th style="text-align:center">Board Name<label><input type="text" name="search_os" value="Board Name" class="search_init" /></label></th>
                             <th style="text-align:center">OS Version<label><input type="text" name="search_os" value="OS Version" class="search_init" /></label></th>
                              <th style="text-align:center">Enable<label><input type="text" name="search_os" value="Enable" class="search_init" /></label></th>
                              <th style="text-align:center">Action<br />&nbsp;</th>
                       </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 0;
                        while($row = $routers->fetch_object()) {
                            $index++;
                            ?>
                            <tr>
                                <td><?php echo $index?></td>
                                <td><?php echo $row->router_ip?></td>
                                <td><?php echo $row->router_identity?></td>
                                <td><?php echo $row->router_location?></td>
                                <td><?php echo $row->board_name?></td>
                                <td><?php echo $row->os_version?></td>
                                <td>
                                <?php 
                                if($row->enable==1)
                                    echo "<span style='color:green'>Enable</span>";
                                else
                                    echo "<span style='color:red'>Disable</span>";
                                ?>
                                </td>
                                <td>
                                            <a class="fa fa-times" title="Delete Router" href="<?php echo SITE_URL?>subviews/routers/routersactions.php?action=delete&router_id=<?php echo $row->routers_id?>"></a>
                                            <a class="fa fa-pencil" title="Edit Router" href="<?php echo SITE_URL?>subviews/routers/routersactions.php?action=edit&router_id=<?php echo $row->routers_id?>"></a> 
                                            <a class="fa fa-refresh" href="<?php echo SITE_URL?>subviews/routers/routersactions.php?action=refresh&router_id=<?php echo $row->routers_id?>" title="Refresh Router"></a>
                                </td>
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


<script type="text/javascript">

$(document).ready(function(){
    
    $('#router_step1').submit(function(){
        $('#step2_result').html('<button type="button" class="btn btn-danger btn-app btn-circle"><i class="fa fa-spinner fa-spin"></i></button>');
        $.post($(this).attr('action'), $(this).serialize(), function(result){
                $('#step2_result').html(result);
            })
        return false;
        })
        
        $(document).on("submit","#router_step2",function() {
            $('#step2_result').html('<button type="button" class="btn btn-danger btn-app btn-circle"><i class="fa fa-spinner fa-spin"></i></button>');
            $.post($(this).attr('action'), $(this).serialize(), function(result){
                    $('#step2_result').html(result);
                })
            return false;
        })
    
    })

</script>


