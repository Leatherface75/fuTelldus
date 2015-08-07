<script src="lib/jscripts/futelldus_togl.js"></script>
<script type="text/javascript">
	$(function() {
        $('.klicka').click(function() {
            var rowId = $(this).attr("row-id");
	    var rowName = $(this).attr("row-action");
            mailControl(rowName, rowId);
        });
        $('.klicka2').click(function() {
            var rowId = $(this).attr("row-id");
	    var rowName = $(this).attr("row-action");
            pushControl(rowName, rowId);
        });
  });
</script>
<?php


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);



	/* Set parameters
	--------------------------------------------------------------------------- */
	if ($action == "editsensor") {
		$query = "SELECT * FROM ".$db_prefix."schedule WHERE notification_id='$getID' LIMIT 1";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array();

	    $sensorID = $row['sensor_id'];
	    $direction = $row['direction'];
	    $warning_value = $row['warning_value'];
	    $type = $row['type'];
	    $repeat_alert = $row['repeat_alert'];
	    $device = $row['device'];
	    $device_set_state = $row['device_set_state'];
	    $send_to_mail_sensor = $row['send_to_mail'];
	    $send_push_sensor = $row['send_push'];
	    $mail_primary_sensor = $row['notification_mail_primary'];
	    $mail_secondary_sensor = $row['notification_mail_secondary'];

	} else {
		$warning_value = 30;
		$repeat_alert = 60;
		$send_to_mail_sensor = 1;
		$send_push_sensor = 0;
		$mail_primary_sensor = $user['mail'];
		$mail_secondary_sensor = "";
	}
	if ($action == "editdevice") {
		$query = "SELECT * FROM ".$db_prefix."schedule_device WHERE notification_id='$getID' LIMIT 1";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array();

	    $deviceID = $row['device_id'];
		$trigger_type = $row['trigger_type'];
		$trigger_state = $row['trigger_state'];
	    $device = $row['action_device'];
	    $device_set_state = $row['action_device_set_state'];
		
	    $repeat_alert = $row['repeat_alert'];
	    $send_to_mail = $row['send_to_mail'];
	    $send_push = $row['send_push'];
	    $mail_primary = $row['notification_mail_primary'];
	    $mail_secondary = $row['notification_mail_secondary'];
	    $push_message = $row['push_message'];

	} else {
		$send_to_mail = 1;
		$trigger_state = 1;
		$repeat_alert = 30;
		$send_push = 0;
		$mail_primary = $user['mail'];
		$mail_secondary = "";
		$push_message = "";
	}


	
	echo "<h4>".$lang['Schedule']."</h4>";

	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-info'>".$lang['Data saved']."</div>";
		elseif ($_GET['msg'] == 02) echo "<div class='alert alert-error'>".$lang['Deleted']."</div>";
	}







	/* Form
	--------------------------------------------------------------------------- */
	
	//Add or Edit Sensor
	if ($action == "addsensor" || $action == "editsensor") {

		if ($action == "editsensor") {
			echo "<div class='alert'>";
			echo "<form action='?page=settings_exec&action=updateSchedule&id=$getID' method='POST'>";
		} else {
			echo "<div class='well'>";
			echo "<form action='?page=settings_exec&action=addSchedule' method='POST'>";
		}


			echo "<table width='100%'>";

				// Sensor
				echo "<tr>";
					echo "<td>{$lang['Sensor']}</td>";
					echo "<td>";
						$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='".$user['user_id']."' AND monitoring='1' ORDER BY name ASC LIMIT 100";
		   				$result = $mysqli->query($query);

		   				echo "<select style='width:180px;' name='sensorID'>";
		   				while ($row = $result->fetch_array()) {
		   					if ($sensorID == $row['sensor_id'])
		   						echo "<option value='{$row['sensor_id']}' selected='selected'>{$row['sensor_id']}: {$row['name']}</option>";

		   					else
		   						echo "<option value='{$row['sensor_id']}'>{$row['sensor_id']}: {$row['name']}</option>";
		   				}
		   				echo "</select>";
					echo "</td>";
				echo "</tr>";


				// Higher / lower
				echo "<tr>";
					echo "<td>{$lang['Type']}</td>";
					echo "<td>";

						echo "<select style='width:120px; margin-right:5px;' name='direction'>";
							if ($direction == "less") $directionSelectedLess = "selected='selected'";
							if ($direction == "more") $directionSelectedMore = "selected='selected'";

							echo "<option value='more' $directionSelectedMore>{$lang['Higher than']}</option>";
							echo "<option value='less' $directionSelectedLess>{$lang['Lower than']}</option>";
						echo "</select>";

						echo "<input style='width:30px; margin-right:5px;' type='text' name='warningValue' id='warningValue' value='$warning_value' />";

						echo "<select style='width:120px;' name='type'>";
							if ($type == "celsius") $typeSelectedCelsius = "selected='selected'";
							if ($type == "humidity") $typeSelectedHumidity = "selected='selected'";

							echo "<option value='celsius' $typeSelectedCelsius>&deg; ({$lang['Celsius']})</option>";
							echo "<option value='humidity' $typeSelectedHumidity>% ({$lang['Humidity']})</option>";
						echo "</select>";

					echo "</td>";
				echo "</tr>";




				


				echo "<tr><td colspan='2'><br /></td></tr>"; // Space






				echo "<tr><td colspan='2'><h5>{$lang['Device action']}</h5></td></tr>"; // Headline


				// Device
				echo "<tr>";
					echo "<td>{$lang['Devices']}</td>";
					echo "<td>";
						$query = "SELECT * FROM ".$db_prefix."devices WHERE user_id='".$user['user_id']."' ORDER BY name ASC LIMIT 100";
		   				$result = $mysqli->query($query);

		   				echo "<select style='width:250px;' name='deviceID'>";
		   					echo "<option value='0'>-- {$lang['No device action']} --</option>";

			   				while ($row = $result->fetch_array()) {
			   					if ($device == $row['device_id'])
			   						echo "<option value='{$row['device_id']}' selected='selected'>{$row['device_id']}: {$row['name']}</option>";

			   					else
			   						echo "<option value='{$row['device_id']}'>{$row['device_id']}: {$row['name']}</option>";
			   				}
		   				echo "</select>";


		   				echo "<select style='width:70px; margin-left:10px;' name='device_action'>";
						if ($device_set_state == 1) {
		   					echo "<option value='1' selected='selected'>{$lang['On']}</option>";
		   					echo "<option value='0'>{$lang['Off']}</option>";
						}
						elseif ($device_set_state == 0) {
		   					echo "<option value='1'>{$lang['On']}</option>";
		   					echo "<option value='0' selected='selected'>{$lang['Off']}</option>";
						}
						else {
		   					echo "<option value='1'>{$lang['On']}</option>";
		   					echo "<option value='0'>{$lang['Off']}</option>";							
						}
		   				echo "</select>";

					echo "</td>";
				echo "</tr>";



				echo "<tr><td colspan='2'><br /></td></tr>"; // Space







				echo "<tr><td colspan='2'><h5>{$lang['Notifications']}</h5></td></tr>"; // Headline


				// Value
				echo "<tr>";
					echo "<td>{$lang['Repeat every']}</td>";
					echo "<td>";
						echo "<input style='width:35px;' type='text' name='repeat' id='repeat' value='$repeat_alert' /> {$lang['minutes']}";
					echo "</td>";
				echo "</tr>";

				// Send to
				echo "<tr>";
					echo "<td>{$lang['Send to']}</td>";
					echo "<td>";
						echo "<label class='checkbox'>";
								if ($send_to_mail_sensor == 1) $sendToMailChecked = "checked='checked'";
					          echo "<input type='checkbox' name='sendTo_mail' value='1' $sendToMailChecked> {$lang['Email']}";
					        echo "</label>";
						if (!empty($userTelldusConf['push_user']) && !empty($userTelldusConf['push_app'])) {
						echo "<label class='checkbox'>";
								if ($send_push_sensor == 1) $sendToPush = "checked='checked'";
					          echo "<input type='checkbox' name='send_push' value='1' $sendToPush> {$lang['Push notifications']}";
					        echo "</label>";
						}
					echo "</td>";
				echo "</tr>";


				// Primary mail
				echo "<tr>";
					echo "<td>{$lang['Primary']} {$lang['Email']}</td>";
					echo "<td>";
						echo "<input style='width:350px;' type='text' name='mail_primary' id='repeat' value='$mail_primary_sensor' />";
					echo "</td>";
				echo "</tr>";

				// Secondary mail
				echo "<tr>";
					echo "<td>{$lang['Secondary']} {$lang['Email']}</td>";
					echo "<td>";
						echo "<input style='width:350px;' type='text' name='mail_secondary' id='repeat' value='$mail_secondary_sensor' />";
					echo "</td>";
				echo "</tr>";



				// Submit
				echo "<tr>";
					echo "<td colspan='2'>";
						echo "<div style='text-align:right;'>";
							if ($action == "edit") echo "<a class='btn' href='?page=settings&view=schedule'>{$lang['Cancel']}</a> &nbsp; ";
							echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Save data']."' />";
						echo "</div>";
					echo "</td>";
				echo "</tr>";

			echo "</table>";
		echo "</form>";
		echo "</div>";
	}
		// Add or Edit device
		if ($action == "adddevice" || $action == "editdevice") {

		if ($action == "editdevice") {
			echo "<div class='alert'>";
			echo "<form action='?page=settings_exec&action=updateSchedule_device&id=$getID' method='POST'>";
		} else {
			echo "<div class='well'>";
			echo "<form action='?page=settings_exec&action=addSchedule_device' method='POST'>";
		}


			echo "<table width='100%'>";

				// Device
				echo "<tr>";
					echo "<td>{$lang['Devices']}</td>";
					echo "<td>";
						$query = "SELECT * FROM ".$db_prefix."devices WHERE user_id='".$user['user_id']."' AND type='device' ORDER BY name ASC LIMIT 100";
		   				$result = $mysqli->query($query);

		   				echo "<select style='width:180px;' name='deviceID'>";
		   				while ($row = $result->fetch_array()) {
		   					if ($deviceID == $row['device_id'])
		   						echo "<option value='{$row['device_id']}' selected='selected'>{$row['device_id']}: {$row['name']}</option>";

		   					else
		   						echo "<option value='{$row['device_id']}'>{$row['device_id']}: {$row['name']}</option>";
		   				}
		   				echo "</select>";

						echo "<select style='width:135px; margin-left:10px;' name='trigger_type'>";
						if ($trigger_type == 1) {
		   					echo "<option value='1' selected='selected'>{$lang['TriggerState_ChangeState']}</option>";
		   					echo "<option value='2'>{$lang['TriggerState_HasState']}</option>";
						}
						elseif ($trigger_type == 2) {
		   					echo "<option value='1'>{$lang['TriggerState_ChangeState']}</option>";
		   					echo "<option value='2' selected='selected'>{$lang['TriggerState_HasState']}</option>";
						}
						else {
		   					echo "<option value='1' selected='selected'>{$lang['TriggerState_ChangeState']}</option>";
		   					echo "<option value='2'>{$lang['TriggerState_HasState']}</option>";							
						}
		   				echo "</select>";
						
						
						echo "<select style='width:70px; margin-left:10px;' name='trigger_state'>";
						if ($trigger_state == 1) {
		   					echo "<option value='1' selected='selected'>{$lang['On']}</option>";
		   					echo "<option value='2'>{$lang['Off']}</option>";
						}
						elseif ($trigger_state == 2) {
		   					echo "<option value='1'>{$lang['On']}</option>";
		   					echo "<option value='2' selected='selected'>{$lang['Off']}</option>";
						}
						else {
		   					echo "<option value='1' selected='selected'>{$lang['On']}</option>";
		   					echo "<option value='2'>{$lang['Off']}</option>";							
						}
		   				echo "</select>";
						
					echo "</td>";
				echo "</tr>";


				echo "<tr><td colspan='2'><br /></td></tr>"; // Space


				// Device action
				
				echo "<tr><td colspan='2'><h5>{$lang['Device action']}</h5></td></tr>"; // Headline

				echo "<tr>";
					echo "<td>{$lang['Devices']}</td>";
					echo "<td>";
						$query = "SELECT * FROM ".$db_prefix."devices WHERE user_id='".$user['user_id']."' ORDER BY name ASC LIMIT 100";
		   				$result = $mysqli->query($query);

		   				echo "<select style='width:250px;' name='action_device'>";
		   					echo "<option value='0'>-- {$lang['No device action']} --</option>";

			   				while ($row = $result->fetch_array()) {
			   					if ($device == $row['device_id'])
			   						echo "<option value='{$row['device_id']}' selected='selected'>{$row['device_id']}: {$row['name']}</option>";

			   					else
			   						echo "<option value='{$row['device_id']}'>{$row['device_id']}: {$row['name']}</option>";
			   				}
		   				echo "</select>";


		   				echo "<select style='width:70px; margin-left:10px;' name='action_device_set_state'>";
						if ($device_set_state == 1) {
		   					echo "<option value='1' selected='selected'>{$lang['On']}</option>";
		   					echo "<option value='0'>{$lang['Off']}</option>";
						}
						elseif ($device_set_state == 0) {
		   					echo "<option value='1'>{$lang['On']}</option>";
		   					echo "<option value='0' selected='selected'>{$lang['Off']}</option>";
						}
						else {
		   					echo "<option value='1'>{$lang['On']}</option>";
		   					echo "<option value='0'>{$lang['Off']}</option>";							
						}
		   				echo "</select>";

					echo "</td>";
				echo "</tr>";


				echo "<tr><td colspan='2'><br /></td></tr>"; // Space

				echo "<tr><td colspan='2'><h5>{$lang['Notifications']}</h5></td></tr>"; // Headline


				// Send to
				echo "<tr>";
					echo "<td>{$lang['Send to']}</td>";
					echo "<td>";
						echo "<label class='checkbox'>";
								if ($send_to_mail == 1) $sendToMailChecked = "checked='checked'";
					          echo "<input type='checkbox' name='sendTo_mail' value='1' $sendToMailChecked> {$lang['Email']}";
					        echo "</label>";
						if (!empty($userTelldusConf['push_user']) && !empty($userTelldusConf['push_app'])) {
						echo "<label class='checkbox'>";
								if ($send_push == 1) $sendToPush = "checked='checked'";
					          echo "<input type='checkbox' name='send_push' value='1' $sendToPush> {$lang['Push notifications']}";
					        echo "</label>";
						}
					echo "</td>";
				echo "</tr>";
				
				// Value
				echo "<tr>";
					echo "<td>{$lang['Repeat every']}</td>";
					echo "<td>";
						echo "<input style='width:35px;' type='text' name='repeat' id='repeat' value='$repeat_alert' /> {$lang['minutes']}";
					echo "</td>";
				echo "</tr>";

				// Push Message
				if (!empty($userTelldusConf['push_user']) && !empty($userTelldusConf['push_app'])) {
					echo "<tr>";
						echo "<td>{$lang['Push message']}</td>";
						echo "<td>";
							echo "<input style='width:350px;' type='text' name='push_message' id='repeat' value='$push_message' />";
						echo "</td>";
					echo "</tr>";
					echo "<tr><td colspan='2'><br /></td></tr>"; // Space
				}

				// Primary mail
				echo "<tr>";
					echo "<td>{$lang['Primary']} {$lang['Email']}</td>";
					echo "<td>";
						echo "<input style='width:350px;' type='text' name='mail_primary' id='repeat' value='$mail_primary' />";
					echo "</td>";
				echo "</tr>";

				// Secondary mail
				echo "<tr>";
					echo "<td>{$lang['Secondary']} {$lang['Email']}</td>";
					echo "<td>";
						echo "<input style='width:350px;' type='text' name='mail_secondary' id='repeat' value='$mail_secondary' />";
					echo "</td>";
				echo "</tr>";



				// Submit
				echo "<tr>";
					echo "<td colspan='2'>";
						echo "<div style='text-align:right;'>";
							if ($action == "edit") echo "<a class='btn' href='?page=settings&view=schedule'>{$lang['Cancel']}</a> &nbsp; ";
							echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Save data']."' />";
						echo "</div>";
					echo "</td>";
				echo "</tr>";

			echo "</table>";
		echo "</form>";
		echo "</div>";
	}







	/* Show notifications
	--------------------------------------------------------------------------- */
	$query = "SELECT * 
			  FROM ".$db_prefix."schedule 
			  INNER JOIN ".$db_prefix."sensors ON ".$db_prefix."schedule.sensor_id = ".$db_prefix."sensors.sensor_id
			  WHERE ".$db_prefix."schedule.user_id='{$user['user_id']}' 
			  ORDER BY ".$db_prefix."schedule.sensor_id ASC";
    $result = $mysqli->query($query);
    $numRows = $result->num_rows;
	echo "<legend>".$lang['Sensors']."</legend>";
	if ($action != "addsensor" && $action != "adddevice" && $action != "editsensor" && $action != "editdevice") {
		echo "<div style='float:right; margin-right:15px;'>";
			echo "<a class='btn btn-success' href='?page=settings&view=schedule&action=addsensor'>" . $lang['Create new'] . "</a>";
		echo "</div>";
	}

    if ($numRows > 0) {

    	echo "<table class='table table-striped table-hover'>";
			echo "<thead>";
				echo "<tr>";
					//echo "<th>".$lang['Name']."</th>";
					echo "<th>".$lang['Rule']."</th>";
					echo "<th>".$lang['Email']."</th>";
					if (!empty($userTelldusConf['push_user']) && !empty($userTelldusConf['push_app']))
					echo "<th>".$lang['Push notifications']."</th>";
					echo "<th>".$lang['Repeat every']."</th>";
					echo "<th>".$lang['Last sent']."</th>";
					echo "<th></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";

		    	while($row = $result->fetch_array()) {

		    		echo "<tr>";


		    			echo "<td>";

		    				// Sensorname
		    				echo "#{$row['sensor_id']}: {$row['name']}<br />";


		    				// Rule description
		    				if ($row['direction'] == "less") $directionDesc = $lang['Lower than'];
		    				elseif ($row['direction'] == "more") $directionDesc = $lang['Higher than'];

		    				if ($row['type'] == "celsius") {
		    					$typeDesc = $lang['Temperature'];
		    					$unit = "&deg;";
		    				}
		    				elseif ($row['type'] == "humidity") {
		    					$typeDesc = $lang['Humidity'];
		    					$unit = "%";
		    				}

		    				echo "{$lang['If']} <b>$typeDesc</b> ".strtolower($lang['Is'])." <b>$directionDesc</b> <b>{$row['warning_value']}" . $unit . "</b>";

		    				if (!empty($row['device'])) {
		    					$getDeviceName = getField("name", "".$db_prefix."devices", "WHERE device_id='{$row['device']}'");
			    				
		    					echo "<br />";
			    				echo "$getDeviceName";
			    				if ($row['device_set_state'] == 1) echo " &nbsp; ({$lang['On']})";
			    				elseif ($row['device_set_state'] == 0) echo " &nbsp; ({$lang['Off']})";
			    			}
		    			echo "</td>";


		    			// Send to mail
		    			echo "<td style='text-align:center;'>";
		    				if ($row['send_to_mail'] == 1) echo "<img style='height:16px;' src='images/metro_black/check.png' alt='yes' />";
		    				else echo "<img style='height:16px;' src='images/metro_black/cancel.png' alt='no' />";
		    			echo "</td>";
					
					
		    			// Send to push
					if (!empty($userTelldusConf['push_user']) && !empty($userTelldusConf['push_app'])) {
		    			echo "<td style='text-align:left;'>";
		    				if ($row['send_push'] == 1) echo "<img style='height:16px;' src='images/metro_black/check.png' alt='yes' />";
		    				else echo "<img style='height:16px;' src='images/metro_black/cancel.png' alt='no' />";
		    			echo "</td>";
					}


		    			// Repeat every
		    			echo "<td>";
		    				echo "{$row['repeat_alert']} {$lang['minutes']}";
		    			echo "</td>";


		    			// Time since last warning
		    			echo "<td>";
		    				if ($row['last_warning'] > 0) echo ago($row['last_warning']);
		    			echo "</td>";


		    			// Toggle tools
		    			echo "<td>";
							echo "<div class='btn-group'>";
								echo "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#''>";
									echo "<span class='caret'></span>";
								echo "</a>";

								echo "<ul class='dropdown-menu pull-right'>";
					    			echo "<li><a href='?page=settings&view=schedule&action=editsensor&id={$row['notification_id']}'>{$lang['Edit']}</a></li>";
					    			echo "<li><a href='?page=settings_exec&action=deleteSchedule&id={$row['notification_id']}'>{$lang['Delete']}</a></li>";
								echo "</ul>";
							echo "</div>";
		    			echo "</td>";

		    		echo "</tr>";

		    	}

    		echo "</tbody>";
    	echo "</table>";
    }

    else echo "<div class='alert'>{$lang['Nothing to display']}</div>";
    
	$query = "SELECT * 
			  FROM ".$db_prefix."schedule_device
			  INNER JOIN ".$db_prefix."devices ON ".$db_prefix."schedule_device.device_id = ".$db_prefix."devices.device_id
			  WHERE ".$db_prefix."devices.user_id='{$user['user_id']}' 
			  ORDER BY ".$db_prefix."schedule_device.device_id ASC";
    $result2 = $mysqli->query($query);
    $numRows = $result2->num_rows;
	echo "<legend>".$lang['Devices']."</legend>";
	if ($action != "addsensor" && $action != "adddevice" && $action != "editsensor" && $action != "editdevice") {
		echo "<div style='float:right; margin-right:15px;'>";
			echo "<a class='btn btn-success' href='?page=settings&view=schedule&action=adddevice'>" . $lang['Create new'] . "</a>";
		echo "</div>";
	}

    if ($numRows > 0) {

    	echo "<table class='table table-striped table-hover'>";
			echo "<thead>";
				echo "<tr>";
					//echo "<th>".$lang['Name']."</th>";
					echo "<th>".$lang['Rule']."</th>";
					echo "<th>".$lang['Action']."</th>";
					echo "<th>".$lang['Email']."</th>";
					if (!empty($userTelldusConf['push_user']) && !empty($userTelldusConf['push_app']))
					echo "<th>".$lang['Push notifications']."</th>";
					echo "<th></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";

		    	while($row = $result2->fetch_array()) {

		    		echo "<tr>";


		    			echo "<td>";

		    				// Rule
		    				echo "#{$row['device_id']}: {$row['name']}<br/>";
							if ($row['trigger_type'] == 1) 
								echo "{$lang['TriggerState_ChangeState']} ";
							else
								echo "{$lang['TriggerState_HasState']} ";
							
							if ($row['trigger_state'] == 1) 
								echo "<b>".$lang['On']."</b>";
							else
								echo "<b>".$lang['Off']."</b>";
							
						echo "</td>";

						echo "<td style='text-align:left;'>";

		    				// Action
		    				if ($row['action_device'] >0 ) echo "<img id='imgDisp{$row['notification_id']}' style='height:16px;' src='images/metro_black/check.png' alt='yes' class='klicka' row-action='0' row-id='{$row['notification_id']}' />";
		    				else echo "<img id='imgDisp{$row['notification_id']}' style='height:16px;' src='images/metro_black/cancel.png' alt='no' class='klicka' row-action='1' row-id='{$row['notification_id']}' />";
		    			echo "</td>";
						


		    			// Send to mail
		    			echo "<td style='text-align:left;'>";
		    				if ($row['send_to_mail'] == 1) echo "<img id='imgDisp{$row['notification_id']}' style='height:16px;' src='images/metro_black/check.png' alt='yes' class='klicka' row-action='0' row-id='{$row['notification_id']}' />";
		    				else echo "<img id='imgDisp{$row['notification_id']}' style='height:16px;' src='images/metro_black/cancel.png' alt='no' class='klicka' row-action='1' row-id='{$row['notification_id']}' />";
		    			echo "</td>";
					
		    			// Send to push
					if (!empty($userTelldusConf['push_user']) && !empty($userTelldusConf['push_app'])) {
		    			echo "<td style='text-align:left;'>";
		    				if ($row['send_push'] == 1) echo "<img id='imgDisp2{$row['notification_id']}' style='height:16px;' src='images/metro_black/check.png' alt='yes' class='klicka2' row-action='0' row-id='{$row['notification_id']}' />";
		    				else echo "<img id='imgDisp2{$row['notification_id']}' style='height:16px;' src='images/metro_black/cancel.png' alt='no' class='klicka2' row-action='1' row-id='{$row['notification_id']}' />";
		    			echo "</td>";
					}


		    			// Toggle tools
		    			echo "<td>";
							echo "<div class='btn-group hidden-phone' style='margin-right: -25px;'>";
								echo "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#''>";
									echo "<span class='caret'></span>";
								echo "</a>";

								echo "<ul class='dropdown-menu pull-right'>";
					    			echo "<li><a href='?page=settings&view=schedule&action=editdevice&id={$row['notification_id']}'>{$lang['Edit']}</a></li>";
					    			echo "<li><a href='?page=settings_exec&action=deleteSchedule_device&id={$row['notification_id']}'>{$lang['Delete']}</a></li>";
								echo "</ul>";
							echo "</div>";
								echo "<div class='btn-group visible-phone'>";
								echo "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#''>";
									echo "<span class='caret'></span>";
								echo "</a>";

								echo "<ul class='dropdown-menu pull-right'>";
					    			echo "<li><a href='?page=settings&view=schedule&action=editdevice&id={$row['notification_id']}'>{$lang['Edit']}</a></li>";
					    			echo "<li><a href='?page=settings_exec&action=deleteSchedule_device&id={$row['notification_id']}'>{$lang['Delete']}</a></li>";
								echo "</ul>";
							echo "</div>";
		    			echo "</td>";

		    		echo "</tr>";

		    	}

    		echo "</tbody>";
    	echo "</table>";
    }

    else echo "<div class='alert'>{$lang['Nothing to display']}</div>";


?>