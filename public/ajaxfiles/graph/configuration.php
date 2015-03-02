<?php



$routers = table('routers')->run();

    

$interfaceTypeIcons = array(

'pppoe-out' => 'ppp-out',
'pptp-out' => 'ppp-out',
'l2tp-out' => 'ppp-out',
'ovpn-out' => 'ppp-out',
'sstp-out' => 'ppp-out',

'pppoe-in' => 'ppp-in',
'pptp-in' => 'ppp-in',
'l2tp-in' => 'ppp-in',
'ovpn-in' => 'ppp-in',
'sstp-in' => 'ppp-in',

'eoip' => 'tunnel',
'ipip-tunnel' => 'tunnel',
'gre-tunnel' => 'tunnel'

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



<div class="row">

<?php

$content_id = 0;

while($router = $routers->fetch_object()){
    $content_id++;
    // check all interfaces existance in router_interfaces
    $router_com = Database::$db->query("SELECT trap_community FROM router_snmp_detail WHERE router_id=".$router->routers_id)->fetch_object()->trap_community;
    $newinterfaces = "SELECT count(interface) FROM(    SELECT  '".implode("' interface UNION SELECT '", interfaces(snmprealwalk($router->router_ip, $router_com, '1.3.6.1.2.1.2.2.1.2')))."' interface) myarray WHERE myarray.interface NOT IN (SELECT interface FROM router_interfaces WHERE router_id=$router->routers_id)";
    $oldinterfaces = "SELECT count(interface) FROM router_interfaces WHERE router_id=$router->routers_id AND  interface NOT IN ('".implode("', '", interfaces(snmprealwalk($router->router_ip, $router_com, '1.3.6.1.2.1.2.2.1.2')))."')";
    $query = "SELECT ($newinterfaces)+($oldinterfaces) as changes";
    if(Database::$db->query($query)->fetch_object()->changes>0){
        refresh_interfaces($router->routers_id, $router->router_ip, $router->router_user, $router->router_pass, $router->router_ssh_port);   
    }
?>
<script>

                function refreshSelected<?php echo $router->routers_id?>(){
                    
                            var actives = {}
                            var bytesactives = {}
                            
                            $( "input[type=checkbox].router_active<?php echo $router->routers_id?>:checked" ).each(function( index ) {
                            //console.log( index + ": " + $( this ).text() );
                                if(actives[$( this ).attr('record_id')]!=undefined)
                                        actives[$( this ).attr('record_id')]++;
                                else
                                        actives[$( this ).attr('record_id')]=1;
                            });
                            
                            $( "input[type=checkbox].router_bytesactive<?php echo $router->routers_id?>:checked" ).each(function( index ) {
                            //console.log( index + ": " + $( this ).text() );
                                if(bytesactives[$( this ).attr('record_id')]!=undefined)
                                        bytesactives[$( this ).attr('record_id')]++;
                                else
                                        bytesactives[$( this ).attr('record_id')]=1;
                            });
                            
                            
                                $('.interfaces_rxtx_<?php echo $router->routers_id?>').html('0')
                            
                            $.each(actives, function(key, value){
                                $('#interfaces_rxtx_<?php echo $router->routers_id?>_'+key).html(value)
                            })
                            
                                $('.interfaces_inout_<?php echo $router->routers_id?>').html('0')
                            
                            $.each(bytesactives, function(key, value){
                                $('#interfaces_inout_<?php echo $router->routers_id?>_'+key).html(value)
                            })
                            
                }

</script>
<form id="form_<?php echo $content_id?>">
	<div class="col-xs-12 col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-arrows-alt"></i>
					<span>Router <span style="color:red; font-weight:600"><?php echo $router->router_identity?></span></span>
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
			<div class="box-content" id="content_<?php echo $content_id?>">
            
                <input type="hidden" name="router_id" value="<?php echo $router->routers_id?>" />
				<table class="table table-hover table-center">
					<thead>
						<tr>
							<th>#</th>
							<th>Type</th>
							<th><span class="fa fa-random"></span> Interfaces</th>
							<th><span class="fa fa-area-chart"></span> RX / TX</th>
							<th><span class="fa fa-bar-chart-o"></span> B In / B Out</th>
							<th><span class="fa fa-plug"></span> Status</th>
						</tr>
					</thead>
					<tbody>
                        
                <?php
                $interfaces = router_mysql_interface_types($router->routers_id, interfaces(snmprealwalk($router->router_ip, $router_com, '1.3.6.1.2.1.2.2.1.2')));
                $statuses = router_mysql_interface_statuses($router->router_ip, $router_com);
                ?>
                
                <?php
                $id = 1;
                foreach($interfaces as $interface => $information){
                ?>
						<tr>
							<td><?php echo $id?></td>
							<td style="min-width:110px; text-align:left;">
                                    <a href="#" class="fa fa-plus-square" style="color:blue" onClick="if($(this).hasClass('fa-minus-square')){$(this).removeClass('fa-minus-square').addClass('fa-plus-square')} else {$(this).removeClass('fa-plus-square').addClass('fa-minus-square')}   $('.interfaces_rows_<?php echo $router->routers_id?>_<?php echo $id?>').toggle('fast'); "> <?php echo htmlentities($interface)?></a>
                            </td>
							<td style="text-align:center"><div id="interfaces_inverse_<?php echo $router->routers_id?>_<?php echo $id?>"><?php echo $information['count']?></div></td>
							<td style="text-align:center"><span class="interfaces_rxtx_<?php echo $router->routers_id?>" id="interfaces_rxtx_<?php echo $router->routers_id?>_<?php echo $id?>">0</span></td>
							<td style="text-align:center"><span class="interfaces_inout_<?php echo $router->routers_id?>" id="interfaces_inout_<?php echo $router->routers_id?>_<?php echo $id?>">0</span></td>
							<td>
                            </td>
						</tr>    
						<tr style="display:none" class="interfaces_rows_<?php echo $router->routers_id?>_<?php echo $id?>">
							<td></td>
							<td></td>
							<td style="text-align:center"></td>
							<td style="text-align:center; min-width:130px;"><a class="label label-default" onClick="$('.router_active<?php echo $router->routers_id.'_'.$id?>').attr('checked', 'checked')">ON All</a> <a class="label label-default" onClick="$('.router_active<?php echo $router->routers_id.'_'.$id?>').removeAttr('checked')">OFF All</a></td>
							<td style="text-align:center; min-width:130px;"><a class="label label-default" onClick="$('.router_bytesactive<?php echo $router->routers_id.'_'.$id?>').attr('checked', 'checked')">ON All</a> <a class="label label-default" onClick="$('.router_bytesactive<?php echo $router->routers_id.'_'.$id?>').removeAttr('checked')">OFF All</a></td>
							<td>
                            </td>
						</tr>    
                        <?php
                        foreach($information['names'] as $snmpid => $info){
                        ?>
                        <tr style="display:none" class="interfaces_rows_<?php echo $router->routers_id?>_<?php echo $id?>">
                            <td></td>
                            <td>
                            <input type="hidden" name="interface_id[<?php echo $snmpid?>]" value="<?php echo htmlentities($info)?>" />
                            <input type="hidden" name="interface_type[<?php echo $snmpid?>]" value="<?php echo $interface?>" />
                            </td>
                            <td style="text-align:left"><img src="<?php echo SITE_URL?>img/icons/<?php if(array_key_exists($interface, $interfaceTypeIcons)) echo $interfaceTypeIcons[$interface]; else echo $interface?>.png" /> <?php echo htmlentities($info)?></td>
                            <td style="padding-left:8%;">
                                <div class="toggle-switch toggle-switch-success">
                                    <label>
                                    <?php
                                        $active = Database::$db->query("SELECT active FROM snmpinterfaces WHERE interface_id=$snmpid AND router_id=".$router->routers_id)->fetch_object()->active;
                                        $bytesactive = Database::$db->query("SELECT bytesactive FROM snmpinterfaces WHERE interface_id=$snmpid AND router_id=".$router->routers_id)->fetch_object()->bytesactive;
                                    ?>
                                        <input type="hidden" name="interface_active[<?php echo $snmpid?>]" value="0">
                                        <input class="router_active<?php echo $router->routers_id?> router_active<?php echo $router->routers_id.'_'.$id?>" type="checkbox" <?php if($active==1){ echo 'checked="checked"'; }?> name="interface_active[<?php echo $snmpid?>]" record_id="<?php echo $id?>" onClick="refreshSelected<?php echo $router->routers_id?>()">
                                        <div class="toggle-switch-inner"></div>
                                        <div class="toggle-switch-switch"><i class="fa fa-check"></i></div>
                                    </label>
                                </div>
                        </td>
                            <td style="padding-left:8%">
                                <div class="toggle-switch toggle-switch-success">
                                    <label>
                                        <input type="hidden" name="interface_bytesactive[<?php echo $snmpid?>]" value="0">
                                        <input class="router_bytesactive<?php echo $router->routers_id?> router_bytesactive<?php echo $router->routers_id.'_'.$id?>" type="checkbox" <?php if($bytesactive==1){ echo 'checked="checked"'; }?> name="interface_bytesactive[<?php echo $snmpid?>]" record_id="<?php echo $id?>" onClick="refreshSelected<?php echo $router->routers_id?>()">
                                        <div class="toggle-switch-inner"></div>
                                        <div class="toggle-switch-switch"><i class="fa fa-check"></i></div>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <img <?php
                                if($statuses[$snmpid]['a'] == 2){
                                    echo "src='" . SITE_URL . "img/icons/red.png' title='disabled'";
                                }
                                elseif($statuses[$snmpid]['o'] == 2){
                                    echo "src='" . SITE_URL . "img/icons/yellow.png' title='disconnected'";
                                }elseif($statuses[$snmpid]['o'] == 1){
                                    echo "src='" . SITE_URL . "img/icons/green.png' title='connected'";
                                }
                                ?> />
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                <?php
                $id++;
                }
                ?>
                
					</tbody>
				</table>
                

			</div>        
                    
               
            
            <div class="col-xs-12 col-sm-12">
                    <div class="box">     
                    
                        <div class="col-xs-12 col-sm-4">
                            <button type="submit" id="submit_<?php echo $content_id?>" class="btn btn-primary btn-block">Submit</button>
                        </div>
                        
                        <div class="col-xs-12 col-sm-4">
                            <div id="content_message_<?php echo $content_id?>" style="valign:middle; display:none; padding:10px;"></div>
                        </div>
                        
                    </div>
			</div>   
                         
                    
                    
    </div>        
        
        <script>
        
        $(document).ready(function(){
            
                $('#submit_<?php echo $content_id?>').click(function(){
                    $('#content_message_<?php echo $content_id?>').removeClass('successAjax').fadeIn().html('<img src="<?php echo SITE_URL?>img/loading.gif" /> <span style="font-size:12pt; margin-left:5px;">Saving changes, please wait ... </span>');
                    $.post('<?php echo SITE_URL?>subviews/graph/savegraph.php', $('#form_<?php echo $content_id?>').serialize(), function(ret){
                        // process result
                        $('#content_message_<?php echo $content_id?>').addClass('successAjax').fadeIn().html('<span style="font-size:12pt; margin-left:5px;">Saved successfully. </span>');
                    });
                    setTimeout(function(){
                        $('#content_message_<?php echo $content_id?>').fadeOut();
                    }, 1500)
                    return false;
                })
                
                refreshSelected<?php echo $router->routers_id?>();
            
            })
        
        </script>
</form>
	</div>
    
<?php
}
?>

</div>

