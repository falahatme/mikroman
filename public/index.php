<?php

header('Content-Type: text/html; charset=utf8');

// database
require_once "config/dispatcher.php";
require_once "../listeners/db.php";
require_once "../ssh.php";
require_once "config/functions.php";
require_once "config/browser.php";
require_once "config/jcalendar.php";


// define no limit for timeout execution.
set_time_limit(0);
$browser = new Browser();

if(strpos($_GET['req'], 'subviews')!==false){
    			if(@strlen($_GET['req'])>0){
				$file = SITE_PATH."ajaxfiles/".$_GET['req'];
                $file = str_replace('subviews/', '', $file);
				if(file_exists($file)){
					require_once $file;
                    die();
                }
            }
    }

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Radiko Network Monitor</title>
		<meta name="description" content="description">
		<meta name="author" content="DevOOPS">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="<?php echo SITE_URL?>plugins/bootstrap/bootstrap.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>offline/font-awesome.min.css" rel="stylesheet">
		<link href='<?php echo SITE_URL?>offline/righteous.css' rel='stylesheet' type='text/css'>
		<link href="<?php echo SITE_URL?>plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>plugins/xcharts/xcharts.min.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>plugins/select2/select2.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>plugins/justified-gallery/justifiedGallery.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>css/style_v1.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>plugins/chartist/chartist.min.css" rel="stylesheet">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
				<script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
				<script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
		<![endif]-->
        <!--End Container-->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <!-- <script src="http://code.jquery.com/jquery.js"></script> -->
        <script src="<?php echo SITE_URL?>plugins/jquery/jquery-1.8.2.min.js"></script>
        <script src="<?php echo SITE_URL?>plugins/jquery-ui/jquery-ui.min.js"></script>
        <script>
        var site_url = '<?php echo SITE_URL?>';
        </script>
        <script src="<?php echo SITE_URL?>js/scripts/calendar.js"></script>
        <script src="<?php echo SITE_URL?>js/scripts/jquery.ui.core.js"></script>
        <script src="<?php echo SITE_URL?>js/scripts/jquery.ui.datepicker-cc.js"></script>
        <script src="<?php echo SITE_URL?>js/scripts/jquery.ui.datepicker-cc-fa.js"></script>

        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo SITE_URL?>plugins/bootstrap/bootstrap.min.js"></script>
        <script src="<?php echo SITE_URL?>plugins/justified-gallery/jquery.justifiedGallery.min.js"></script>
        <script src="<?php echo SITE_URL?>plugins/tinymce/tinymce.min.js"></script>
        <script src="<?php echo SITE_URL?>plugins/tinymce/jquery.tinymce.min.js"></script>
        <!-- All functions for this theme + document.ready processing -->
        <script src="<?php echo SITE_URL?>js/devoops.js"></script>
	</head>
<body>
<!--Start Header-->
<div id="screensaver">
	<canvas id="canvas"></canvas>
	<i class="fa fa-lock" id="screen_unlock"></i>
</div>
<div id="modalbox">
	<div class="devoops-modal">
		<div class="devoops-modal-header">
			<div class="modal-header-name">
				<span>Basic table</span>
			</div>
			<div class="box-icons">
				<a class="close-link">
					<i class="fa fa-times"></i>
				</a>
			</div>
		</div>
		<div class="devoops-modal-inner">
		</div>
		<div class="devoops-modal-bottom">
		</div>
	</div>
</div>
<header class="navbar">
	<div class="container-fluid expanded-panel">
		<div class="row">
			<div id="logo" class="col-xs-12 col-sm-2">
				<a href="<?php echo SITE_URL?>index.php">Mikroman V1</a>
			</div>
			<div id="top-panel" class="col-xs-12 col-sm-10">
				<div class="row">
					<div class="col-xs-8 col-sm-4">
						<div id="search">
							<!--input type="text" placeholder="search"/>
							<i class="fa fa-search"></i-->
						</div>
					</div>
					<div class="col-xs-4 col-sm-8 top-panel-right">

                        <?php

                        $query = "SELECT
                                    NOW() > ((SELECT value FROM server_settings WHERE setting='last_listen_time')
                                    + INTERVAL
                                                    (SELECT value FROM server_settings WHERE setting='data_timeout')
                                                SECOND) as data_status
                                    FROM `server_settings` LIMIT 0,1
                        ";
                        if(Database::$db->query($query)->fetch_object()->data_status==0){
                            ?>
                            <span style="font-family: calibri; font-size: 12pt; color:#22ab22; margin: auto 5px;">Data <img src="<?php echo SITE_URL?>img/enable.png" /></span>
                        <?php
                        }else{
                            ?>
                            <span style="font-family: calibri; font-size: 12pt; color:#dd0000; margin: auto 5px;">Data <img src="<?php echo SITE_URL?>img/disable.png" /></span>
                        <?php
                        }

                        ?>

                        <?php

                        if(pingDomain('192.168.1.1')!=-1){
                            ?>
                            <span style="font-family: calibri; font-size: 12pt; color:#22ab22; margin: auto 5px;">Connection <img src="<?php echo SITE_URL?>img/enable.png" /></span>
                        <?php
                        }else{
                            ?>
                            <span style="font-family: calibri; font-size: 12pt; color:#dd0000; margin: auto 5px;">Connection <img src="<?php echo SITE_URL?>img/disable.png" /></span>
                        <?php
                        }

                        ?>

						<ul class="nav navbar-nav pull-right panel-menu">
							<!--li class="hidden-xs">
								<a href="<?php echo SITE_URL?>index.html" class="modal-link">
									<i class="fa fa-bell"></i>
									<span class="badge">7</span>
								</a>
							</li>
							<li class="hidden-xs">
								<a class="ajax-link" href="<?php echo SITE_URL?>calendar.html">
									<i class="fa fa-calendar"></i>
									<span class="badge">7</span>
								</a>
							</li>
							<li class="hidden-xs">
								<a href="<?php echo SITE_URL?>page_messages.html" class="ajax-link">
									<i class="fa fa-envelope"></i>
									<span class="badge">7</span>
								</a>
							</li-->
							<li class="dropdown">
								<a href="#" class="dropdown-toggle account" data-toggle="dropdown">
									<div class="avatar">
										<!--img src="img/avatar.jpg" class="img-circle" alt="avatar" /-->
									</div>
									<i class="fa fa-angle-down pull-right"></i>
									<div class="user-mini pull-right">
										<span class="welcome">Welcome,</span>
										<span>Admin</span>
									</div>
								</a>
								<ul class="dropdown-menu">
									<!--li>
										<a href="#">
											<i class="fa fa-user"></i>
											<span>Profile</span>
										</a>
									</li>
									<li>
										<a href="<?php echo SITE_URL?>page_messages.html" class="ajax-link">
											<i class="fa fa-envelope"></i>
											<span>Messages</span>
										</a>
									</li>
									<li>
										<a href="<?php echo SITE_URL?>gallery_simple.html" class="ajax-link">
											<i class="fa fa-picture-o"></i>
											<span>Albums</span>
										</a>
									</li>
									<li>
										<a href="<?php echo SITE_URL?>calendar.html" class="ajax-link">
											<i class="fa fa-tasks"></i>
											<span>Tasks</span>
										</a>
									</li-->
									<li>
										<a href="#">
											<i class="fa fa-cog"></i>
											<span>Settings</span>
										</a>
									</li>
									<li>
										<a href="#">
											<i class="fa fa-power-off"></i>
											<span>Logout</span>
										</a>
									</li>
								</ul>
							</li>
						</ul>


					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!--End Header-->
<!--Start Container-->
<div id="main" class="container-fluid">
	<div class="row">
		<div id="sidebar-left" class="col-xs-2 col-sm-2">
			<ul class="nav main-menu">
				<li>
					<a href="<?php echo SITE_URL?>dashboard.php" class="active ajax-link">
						<i class="fa fa-dashboard"></i>
						<span class="hidden-xs">Dashboard</span>
					</a>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-users"></i>
						<span class="hidden-xs">Users Manager</span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="ajax-link" href="<?php echo SITE_URL?>charts_xcharts.php">xCharts</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>charts_flot.php">Flot Charts</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>charts_google.php">Google Charts</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>charts_morris.php">Morris Charts</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>charts_amcharts.php">AmCharts</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>charts_chartist.php">Chartist</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>charts_coindesk.php">CoinDesk realtime</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-signal"></i>
						 <span class="hidden-xs">Wireless Manager</span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="ajax-link" href="<?php echo SITE_URL?>tables_simple.php">Simple Tables</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>tables_datatables.php">Data Tables</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>tables_beauty.php">Beauty Tables</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-globe"></i>
						 <span class="hidden-xs">WebProxy Logger</span>
					</a>
					<ul class="dropdown-menu">
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>webproxy/general.php">General</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>webproxy/report.php">Report</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>forms_elements.php">Settings</a></li>

                        <li><a class="ajax-link" href="<?php echo SITE_URL?>forms_elements.php">Elements</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>forms_layouts.php">Layouts</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>forms_file_uploader.php">File Uploader</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-eye"></i>
						 <span class="hidden-xs">System Logger</span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="ajax-link" href="<?php echo SITE_URL?>ui_grid.php">Grid</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>ui_buttons.php">Buttons</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>ui_progressbars.php">Progress Bars</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>ui_jquery-ui.php">Jquery UI</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>ui_icons.php">Icons</a></li>
					</ul>
				</li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class="fa fa-random"></i>
                        <span class="hidden-xs">Interface</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo SITE_URL?>page_login_v1.php">Login</a></li>
                        <li><a href="<?php echo SITE_URL?>page_register_v1.php">Register</a></li>
                        <li><a id="locked-screen" class="submenu" href="<?php echo SITE_URL?>page_locked.php">Locked Screen</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_contacts.php">Contacts</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_feed.php">Feed</a></li>
                        <li><a class="ajax-link add-full" href="<?php echo SITE_URL?>page_messages.php">Messages</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_pricing.php">Pricing</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_product.php">Product</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_invoice.php">Invoice</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_search.php">Search Results</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_404.php">Error 404</a></li>
                        <li><a href="<?php echo SITE_URL?>page_500_v1.php">Error 500</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class="fa fa-th-list"></i>
                        <span class="hidden-xs">AAA</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo SITE_URL?>page_login_v1.php">Login</a></li>
                        <li><a href="<?php echo SITE_URL?>page_register_v1.php">Register</a></li>
                        <li><a id="locked-screen" class="submenu" href="<?php echo SITE_URL?>page_locked.php">Locked Screen</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_contacts.php">Contacts</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_feed.php">Feed</a></li>
                        <li><a class="ajax-link add-full" href="<?php echo SITE_URL?>page_messages.php">Messages</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_pricing.php">Pricing</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_product.php">Product</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_invoice.php">Invoice</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_search.php">Search Results</a></li>
                        <li><a class="ajax-link" href="<?php echo SITE_URL?>page_404.php">Error 404</a></li>
                        <li><a href="<?php echo SITE_URL?>page_500_v1.php">Error 500</a></li>
                    </ul>
                </li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-arrows-alt"></i>
						<span class="hidden-xs">Router</span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="ajax-link" href="<?php echo SITE_URL?>routers/routers.php">Devices</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>maps.php">OpenStreetMap</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>map_fullscreen.php">Fullscreen map</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>map_leaflet.php">Leaflet</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-area-chart"></i>
						<span class="hidden-xs">Graph</span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="ajax-link" href="<?php echo SITE_URL?>graph/configuration.php">Configuration</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">View</span>
							</a>
							<ul class="dropdown-menu">
						<?php
						$routers = Database::$db->query("SELECT * FROM routers WHERE routers_id IN (SELECT router_id as routers_id FROM `snmpinterfaces` WHERE active=1 OR bytesactive=1 GROUP BY routers_id)");
						while($row = $routers->fetch_object()){
						?>
						<li><a href="<?php echo SITE_URL?>graph/view.php?router_id=<?php echo $row->routers_id?>"><?php echo $row->router_identity?></a></li>
						<?php
						}
						?>
							</ul>
						</li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-wrench"></i>
						 <span class="hidden-xs">Tools</span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="ajax-link" href="<?php echo SITE_URL?>gallery_simple.php">Simple Gallery</a></li>
						<li><a class="ajax-link" href="<?php echo SITE_URL?>gallery_flickr.php">Flickr Gallery</a></li>

					</ul>
				</li>
				<li>
					 <a class="ajax-link" href="<?php echo SITE_URL?>typography.php">
						 <i class="fa fa-cogs"></i>
						 <span class="hidden-xs">Settings</span>
					</a>
				</li>
				 <li>
					<a class="ajax-link" href="<?php echo SITE_URL?>calendar.php">
						 <i class="fa fa-calendar"></i>
						 <span class="hidden-xs">Calendar</span>
					</a>
				 </li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-picture-o"></i>
						 <span class="hidden-xs">Multilevel menu</span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="#">First level menu</a></li>
						<li><a href="#">First level menu</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">Second level menu group</span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#">Second level menu</a></li>
								<li><a href="#">Second level menu</a></li>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle">
										<i class="fa fa-plus-square"></i>
										<span class="hidden-xs">Three level menu group</span>
									</a>
									<ul class="dropdown-menu">
										<li><a href="#">Three level menu</a></li>
										<li><a href="#">Three level menu</a></li>
										<li class="dropdown">
											<a href="#" class="dropdown-toggle">
												<i class="fa fa-plus-square"></i>
												<span class="hidden-xs">Four level menu group</span>
											</a>
											<ul class="dropdown-menu">
												<li><a href="#">Four level menu</a></li>
												<li><a href="#">Four level menu</a></li>
												<li class="dropdown">
													<a href="#" class="dropdown-toggle">
														<i class="fa fa-plus-square"></i>
														<span class="hidden-xs">Five level menu group</span>
													</a>
													<ul class="dropdown-menu">
														<li><a href="#">Five level menu</a></li>
														<li><a href="#">Five level menu</a></li>
														<li class="dropdown">
															<a href="#" class="dropdown-toggle">
																<i class="fa fa-plus-square"></i>
																<span class="hidden-xs">Six level menu group</span>
															</a>
															<ul class="dropdown-menu">
																<li><a href="#">Six level menu</a></li>
																<li><a href="#">Six level menu</a></li>
															</ul>
														</li>
													</ul>
												</li>
											</ul>
										</li>
										<li><a href="#">Three level menu</a></li>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<!--Start Content-->
		<div id="content" class="col-xs-12 col-sm-10">




			<div id="about">
				<div class="about-inner">
					<h4 class="page-header">Open-source admin theme for you</h4>
					<p>DevOOPS team</p>
					<p>Homepage - <a href="<?php echo SITE_URL?>http://devoops.me" target="_blank">http://devoops.me</a></p>
					<p>Email - <a href="<?php echo SITE_URL?>mailto:devoopsme@gmail.com">devoopsme@gmail.com</a></p>
					<p>Twitter - <a href="<?php echo SITE_URL?>http://twitter.com/devoopsme" target="_blank">http://twitter.com/devoopsme</a></p>
					<p>Donate - BTC 123Ci1ZFK5V7gyLsyVU36yPNWSB5TDqKn3</p>
				</div>
			</div>
			<div id="ajax-content">


			<?php
			if(@strlen($_GET['req'])>0){
				$file = SITE_PATH."ajaxfiles/".$_GET['req'];
				if(file_exists($file)){
					require_once $file;
				}else{
                    require_once SITE_PATH."ajaxfiles/dashboard.php";
                }
			}else{
                require_once SITE_PATH."ajaxfiles/dashboard.php";
            }
			?>


			</div>
		</div>
		<!--End Content-->
	</div>
</div>

</body>
</html>
