<?php
	require("lib/base.inc.php");
	require("lib/auth.php");
	
	
	// Update last_active in database for active user
	if ($config['log_activity'] != 0) {
		$query = "UPDATE ".$db_prefix."users SET  
			last_active='".time()."'
			WHERE user_id='".$user['user_id']."'";
		$result = $mysqli->query($query);
	}
?>


<!DOCTYPE html>
<html lang="en">
<head>


	<meta charset="utf-8">
    <title><?php echo $config['pagetitle']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="shortcut icon" href="favicon.png?rel=1.1">

	

	<!-- Jquery -->
	<script src="lib/packages/jquery/jquery-1.9.1.min.js"></script>

	<script src="lib/packages/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
	<link href="lib/packages/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet">

	<script src="lib/packages/timeago_jquery/jquery.timeago.js"></script>
	<?php
		if ($defaultLang == "no") echo "<script src=\"lib/packages/timeago_jquery/jquery.timeago.no.js\"></script>";
		if ($defaultLang == "sw") echo "<script src=\"lib/packages/timeago_jquery/jquery.timeago.sw.js\"></script>";
	?>


	<!-- Bootstrap framework -->
	<script src="lib/packages/bootstrap/js/bootstrap.min.js"></script>
	<link href="lib/packages/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="lib/packages/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">


	<link href="css/pagestyle.css" rel="stylesheet">


	<!-- For iPhone 4 Retina display: -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/thermometer.png">

	<!-- For iPad: -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/thermometer.png">

	<!-- For iPhone: -->
	<link rel="apple-touch-icon-precomposed" href="images/thermometer.png">




	<script type="text/javascript">
		idleTime = 0;
		
		$(document).ready(function () {
			
		// IOS Webapp open links in same window instead of open safari
		if (("standalone" in window.navigator) && window.navigator.standalone) {
			// For iOS Apps
			$('a').on('click', function(e){
			e.preventDefault();
			var new_location = $(this).attr('href');
			
			if (new_location != undefined && new_location.substr(0, 1) != '#' && $(this).attr('data-method') == undefined){
				window.location = new_location;
			}
			
			});
		}
		
		
		    //Increment the idle time counter every minute.
		    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

		    //Zero the idle timer on mouse movement.
		    $(this).mousemove(function (e) {
		        idleTime = 0;
		    });
		    $(this).keypress(function (e) {
		        idleTime = 0;
		    });
		});
		
		function timerIncrement() {
		    idleTime = idleTime + 1;
		    if (idleTime > 9) { // 10 minutes
		        window.location.reload();
		    }
		}
	</script>

</head>
<body>

	<div class="container">
		
		<div class="masthead visible-phone" style='z-index: 1; width: 100%; position: fixed; background-color:#1919D1; margin:-20px -20px 15px -20px'>
			<div style='padding:5px 5px;'>

				<div class="pull-left" style='font-size:22px; font-weight:bold; color:#fff; padding:3px 5px;'>
					<a style='color:#fff;' href='index.php'>
						<img style='height:30px;' src="images/logo.png" alt="brand" />
						<?php echo $config['pagetitle']; ?>
					</a>
				</div>

				<div class="btn-group pull-right">
				  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    <i class="icon-th-list"></i>
				    <span class="caret"></span>
				  </a>
				  <ul class="dropdown-menu">
				    <li><a href='index.php'><?php echo $lang['Home']; ?></a></li>
				    <li><a href='?page=sensors'><?php echo $lang['Sensors']; ?></a></li>
				    <li><a href='?page=devices'><?php echo $lang['Lights']; ?></a></li>
				    <li><a href='?page=events'><?php echo $lang['Events']; ?></a></li>
				    <li><a href='?page=settings&view=schedule'><?php echo $lang['Schedule']; ?></a></li>
				    <li><a href='?page=settings'><?php echo $lang['Settings']; ?></a></li>
				    <li class="divider"></li>
				    <li><a href='login/logout.php'><?php echo $lang['Log out']; ?></a></li>
				  </ul>
				</div>

				<div class='clearfix'></div>
			</div>
		</div>

		<div class="container">
		<div class="masthead hidden-phone">
			
			<?php if ($config['navbar_layout'] != "grey") { ?>
			<nav class="navbar navbar-inverse">
				<div class="navbar-inner">
					<div class="container">

						<ul class="nav navbar-nav">
						   <li style='vertical-align: middle;'><img style='float: left; height:30px;' src="images/logo.png" alt="brand" /></li>
						   <li style='vertical-align: middle;'><p class="visible-desktop" style='color:#ffffff; font-size:30px; font-weight: bold; text-decoration: underline; text-align: right;'>
							<!--<img style='float: left; height:30px;' src="images/logo.png" alt="brand" />-->
							<?php echo $config['pagetitle']; ?>
							</p>
							<p class="visible-tablet" style='color:#ffffff; font-size:15px; font-weight: bold; text-decoration: underline;'>
							<!--<img style='float: left; height:20px;' src="images/logo.png" alt="brand" />-->
							<?php echo $config['pagetitle']; ?>
							</p>
						   </li>

							<?php
								// Set menuelements as active
								if (!isset($_GET['page']) || $_GET['page'] == "mainpage") $navMainpage_active = "active";
								elseif (substr($_GET['page'], 0, 7) == "sensors") $navSensors_active = "active";
								elseif (substr($_GET['page'], 0, 7) == "devices" || substr($_GET['page'], 0, 7) == "history" ) $navDevices_active = "active";
								elseif (substr($_GET['page'], 0, 6) == "events") $navEvents_active = "active";
								elseif (substr($_GET['page'], 0, 5) == "chart") $navChart_active = "active";
								elseif (substr($_GET['page'], 0, 8) == "settings") $navSettings_active = "active";
							?>


							<li class="<?php echo $navMainpage_active; ?>"><a href="index.php"><?php echo $lang['Home']; ?></a></li>
							<li class="<?php echo $navSensors_active; ?>"><a href="?page=sensors"><?php echo $lang['Sensors']; ?></a></li>
							<li class="<?php echo $navDevices_active; ?>"><a href="?page=devices"><?php echo $lang['Lights']; ?></a></li>
							<li class="<?php echo $navEvents_active; ?>"><a href="?page=events"><?php echo $lang['Events']; ?></a></li>
							<li class="<?php echo $navChart_active; ?>"><a href="?page=chart"><?php echo $lang['Chart']; ?></a></li>
							<li class="dropdown">					
								<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button">
									<i class=" hidden-tablet icon-user"></i>
									<?php echo $user['mail']; ?>
									<span class="caret"></span>
								</a>

								<ul class="dropdown-menu pull-right" role="menu">
									<?php
										echo "<li><a href='?page=settings&view=user&action=edit&id={$user['user_id']}'>".$lang['My profile']."</a>";
										echo "<a href='./public/index.php'>".$lang['View public page']."</a>";
										echo "<a href='?page=settings'>".$lang['Settings']."</a>";
										echo "<a href='./login/logout.php'>".$lang['Log out']."</a></li>";
									?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav><!-- /.navbar -->
			
			<?php } else { ?>
			
						<h3 class="muted">
				<a href='index.php'>
					<img style='height:30px;' src="images/logo.png" alt='logo' />
					<?php echo $config['pagetitle']; ?>
				</a>
			</h3>

			<div style="float:right; margin-top:-45px; margin-right:15px;">
				<div class="btn-group">					
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<?php
							echo $user['mail']; 
						?>
						<span class="caret"></span>
					</a>

					<ul class="dropdown-menu">
						<?php
							echo "<li><a href='?page=settings&view=user'>".$lang['My profile']."</a></li>";
							echo "<li><a href='./public/index.php'>".$lang['View public page']."</a></li>";
							echo "<li><a href='./login/logout.php'>".$lang['Log out']."</a></li>";
						?>
					</ul>
				</div>

			</div>
			
			<div class="navbar">
				<div class="navbar-inner">
					<div class="container">
						<ul class="nav">

							<?php
								// Set menuelements as active
								if (!isset($_GET['page']) || $_GET['page'] == "mainpage") $navMainpage_active = "active";
								elseif (substr($_GET['page'], 0, 7) == "sensors") $navSensors_active = "active";
								elseif (substr($_GET['page'], 0, 7) == "devices") $navDevices_active = "active";
								elseif (substr($_GET['page'], 0, 5) == "chart") $navChart_active = "active";
								elseif (substr($_GET['page'], 0, 8) == "settings") $navSettings_active = "active";
							?>


							<li class="<?php echo $navMainpage_active; ?>"><a href="index.php"><?php echo $lang['Home']; ?></a></li>
							<li class="<?php echo $navSensors_active; ?>"><a href="?page=sensors"><?php echo $lang['Sensors']; ?></a></li>
							<li class="<?php echo $navDevices_active; ?>"><a href="?page=devices"><?php echo $lang['Lights']; ?></a></li>
							<li class="<?php echo $navChart_active; ?>"><a href="?page=chart"><?php echo $lang['Chart']; ?></a></li>
							<li class='<?php echo $navSettings_active; ?>'><a href="?page=settings"><?php echo $lang['Settings']; ?></a></li>
						</ul>
					</div>
				</div>
			</div><!-- /.navbar -->

			
			<?php } ?>

			
		</div>
		</div>
		<div class='visible-phone' style='height: 40px;'></div>


		<?php include("include_script.inc.php"); ?>


	<div class='clearfix'></div> 

		<div class='hidden-phone' style='text-align:center; border-top:1px solid #eaeaea; font-size:10px; margin-top:35px; color:#c7c7c7;'>
			Developed by <a href='http://www.fosen-utvikling.no'>Fosen Utvikling</a> &nbsp;&nbsp;|&nbsp;&nbsp; Modifications by <a href='http://www.mackapaer.se'>csoM Solutions</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="mailto:<?php echo $config['mail_from'] ?>">Support</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			Last load: <?php echo date("Y-m-d H:i"); ?>

			<br />
			This work is licensed under a <a href='http://creativecommons.org/licenses/by-nc/3.0/'>Creative Commons Attribution-NonCommercial 3.0 Unported License</a>.
		</div>


	</div> <!-- /container -->

</body>
</html>