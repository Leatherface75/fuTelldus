<?php

	/* Connect to database
	--------------------------------------------------------------------------- */
	require("lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");

	/* Get oAuth class
	--------------------------------------------------------------------------- */
	require_once 'HTTP/OAuth/Consumer.php';









	/* ##################################################################################################################### */
	/* ######################################## SCRIPT RUNS BELOW THIS LINE ################################################ */
	/* ##################################################################################################################### */



	$query = "SELECT * 
			  FROM ".$db_prefix."schedule 
			  INNER JOIN ".$db_prefix."sensors ON ".$db_prefix."schedule.sensor_id = ".$db_prefix."sensors.sensor_id
			  INNER JOIN ".$db_prefix."users ON ".$db_prefix."schedule.user_id = ".$db_prefix."users.user_id
			  ";
    $result = $mysqli->query($query);
    $numRows = $result->num_rows;

    if ($numRows > 0) {
    	while($row = $result->fetch_array()) {
		// Get keys from owner of sensor
		$query3 = "SELECT * FROM ".$db_prefix."users_telldus_config WHERE user_id='{$row['user_id']}'";
			$result3 = $mysqli->query($query3);
			$telldusConf2 = $result3->fetch_array();

    		$scheduleRun = false;	// Set default as false


    		/* PRINT DATA FOR DEBUG
    		echo "Notification ID: {$row['notification_id']} <br />";
    		echo "Sensorname: {$row['name']} <br />";
    		echo "Sensor ID: {$row['sensor_id']} <br />";
    		echo "Device ID: {$row['device']} <br />";
    		*/


    		/* Get last sensor values logged
			--------------------------------------------------------------------------- */

    		// Check only for temp values newer than
    		$newerThan = (time() - 3600); // 3600 = 1 hour

    		$queryTemp = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row['sensor_id']}' AND time_updated > '$newerThan' ORDER BY time_updated DESC LIMIT 1";
    		$resultTemp = $mysqli->query($queryTemp);
    		$tempData = $resultTemp->fetch_array();





	    	/* Check for warning last sent
			--------------------------------------------------------------------------- */
			$timeSinceLastWarning = (time() - $row['last_warning']);


			// Check if notification-repeat-state
			if (($row['repeat_alert'] * 60) < $timeSinceLastWarning) $repeatNotification = true;
			else $repeatNotification = false;

			// Define notification type text
			if ($row['notification_type'] == 1)
				$notifyType = $lang['Info'];
			else 
				$notifyType = $lang['Warning'];


			echo "<br><br>Sensor name: " . $row['name'];
			
			/* Check for device action
			--------------------------------------------------------------------------- */

			/* Don't run if last action was less than SET-secounds
			 *   This is to prevent sending data every time
			 * 15 minutes => 900 sec
			 * 30 minutes => 1800 sec
			 * 60 minutes => 3600 sec
			*/
			
			if (900 < $timeSinceLastWarning) $repeatDeviceState = true;
			else $repeatDeviceState = false;

			if (empty($row['device'])) $repeatDeviceState = false; // Check for device
			
			// Connect to Telldus Live
			else {
				require_once 'HTTP/OAuth/Consumer.php';
				$consumer = new HTTP_OAuth_Consumer($telldusConf2['public_key'], $telldusConf2['private_key'], $telldusConf2['token'], $telldusConf2['token_secret']);

				if ($row['device_set_state'] == 1) $deviceStateApiParameter = "turnOn";
				else $deviceStateApiParameter = "turnOff";
			}
			









	    	/* Check for LESS THAN
			--------------------------------------------------------------------------- */
	    	if ($row['direction'] == "less") {

	    		// CELSIUS
	    		if ($row['type'] == "celsius") {
			    	if ($tempData['temp_value'] < $row['warning_value']) {
			    		//echo "<b>WARNING: Temperature is less</b><br />{$lang['Notification mail low temperature']}<br /><br />";

			    		// Send notification
			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$lang['Low']} {$lang['Temperature']}";
				    		$mailMessage = "{$lang['Notification mail low temperature']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['temp_value'], $mailMessage);
												
				    		$pushMessage = "{$lang['Push mail_notification']}";

				    		$pushMessage = str_replace("%%sensor%%", $row['name'], $pushMessage);
							$pushMessage = str_replace("%%value%%", $tempData['temp_value'], $pushMessage);
						}


						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }

			    // HUMIDITY
			    elseif ($row['type'] == "humidity") {
			    	if ($tempData['humidity_value'] < $row['warning_value']) {
			    		//echo "<b>WARNING: Humidity is less</b><br />";

			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$lang['Low']} {$lang['Humidity']}";
				    		$mailMessage = "{$lang['Notification mail low humidity']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['humidity_value'], $mailMessage);
												
				    		$pushMessage = "{$lang['Push mail_notification']}";

				    		$pushMessage = str_replace("%%sensor%%", $row['name'], $pushMessage);
							$pushMessage = str_replace("%%value%%", $tempData['humidity_value'], $pushMessage);
						}

						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }
		    }


		    /* Check for MORE THAN
			--------------------------------------------------------------------------- */
	    	if ($row['direction'] == "more") {

	    		// CELSIUS
	    		if ($row['type'] == "celsius") {
			    	if ($tempData['temp_value'] > $row['warning_value']) {
			    		//echo "<b>WARNING: Temperature is more</b><br />{$lang['Notification mail high temperature']}<br /><br />";
			    		
			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$lang['High']} {$lang['Temperature']}";
				    		$mailMessage = "{$lang['Notification mail high temperature']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['temp_value'], $mailMessage);
												
				    		$pushMessage = "{$lang['Push mail_notification']}";

				    		$pushMessage = str_replace("%%sensor%%", $row['name'], $pushMessage);
							$pushMessage = str_replace("%%value%%", $tempData['temp_value'], $pushMessage);
						}

						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }

			    // HUMIDITY
			    elseif ($row['type'] == "humidity") {
			    	if ($tempData['humidity_value'] > $row['warning_value']) {
			    		//echo "<b>WARNING: Humidity is more</b><br />";

			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$lang['High']} {$lang['Humidity']}";
				    		$mailMessage = "{$lang['Notification mail high humidity']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['humidity_value'], $mailMessage);
					
				    		$pushMessage = "{$lang['Push mail_notification']}";

				    		$pushMessage = str_replace("%%sensor%%", $row['name'], $pushMessage);
							$pushMessage = str_replace("%%value%%", $tempData['humidity_value'], $pushMessage);
						}

						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }
		    }



		    /* IF warning = true
			--------------------------------------------------------------------------- */
		    if ($scheduleRun) {
				echo "<br><b>Action taken!</b>";
		    	// Update sent timestamp
			    $queryTimestamp = "UPDATE ".$db_prefix."schedule SET last_warning='".time()."' WHERE notification_id='".$row['notification_id']."'";
				$resultTimestamp = $mysqli->query($queryTimestamp);
				

				// Send mail
				if ($row['send_to_mail'] == 1 && $repeatNotification == true) {

					// Use mail-function in /lib/php_functions/global.functions.inc.php
					if (!empty($row['notification_mail_primary'])) sendMail($row['notification_mail_primary'], $mailSubject, $mailMessage);
					if (!empty($row['notification_mail_secondary'])) sendMail($row['notification_mail_secondary'], $mailSubject, $mailMessage);
				}
				// Send push
					if ($row['send_push'] == 1 && !empty($telldusConf2['push_user']) && !empty($telldusConf2['push_app']) && $repeatNotification == true) {
						
					// Use PushOver curl
					sendPush("{$telldusConf2['push_app']}", "{$telldusConf2['push_user']}", "{$mailSubject}", "{$pushMessage}");
					}
			} else {
				echo "<br>No action taken this time!";
			}
				

    	} //end-while
    } //end-numRows
    
    

		//    Run device schedule
		//   -----------------------------------------------------------------------------------

	$query = "SELECT * 
			  FROM ".$db_prefix."schedule_device
			  INNER JOIN ".$db_prefix."devices ON ".$db_prefix."schedule_device.device_id = ".$db_prefix."devices.device_id
			  INNER JOIN ".$db_prefix."users ON ".$db_prefix."schedule_device.user_id = ".$db_prefix."users.user_id
			  ";
    $result6 = $mysqli->query($query);
    $numRows2 = $result6->num_rows;

    if ($numRows2 > 0) {
    	while($row4 = $result6->fetch_array()) {
				    	/* Check for warning last sent
			--------------------------------------------------------------------------- */
			if ($row4['send_to_mail'] == 1 || $row4['send_push'] == 1 || $row4['action_device'] > 0 ) {
			
				/* Connect to telldus
				--------------------------------------------------------------------------- */
				$query2 = "SELECT * FROM ".$db_prefix."users_telldus_config WHERE user_id='{$row4['user_id']}'";
				$result2 = $mysqli->query($query2);
				$telldusConf = $result2->fetch_array();
				
				$consumer = new HTTP_OAuth_Consumer($telldusConf['public_key'], $telldusConf['private_key'], $telldusConf['token'], $telldusConf['token_secret']);

				$params = array('id'=> $row4['device_id'], 'supportedMethods'=> 3);
				$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/info', $params, 'GET');

				$xmlString = $response->getBody();
				$xmldata = new SimpleXMLElement($xmlString);
				
				if ($row4['notification_type'] == 1)
					$notifyType = $lang['Info'];
				else 
					$notifyType = $lang['Warning'];
						
				if (($row4['trigger_type'] == 1) && ($row4['trigger_state'] == 1)) {  //If trigger is 'Device change state to On'
					if (($xmldata->state == 1) && ($row4['device_state'] == 2)) { 	// current state is On, last state was Off
						$take_action = true;
						$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$row4['name']} {$lang['DeviceTurnedOn']}";
						$mailMessage = "{$notifyType}: {$row4['name']} {$lang['HasChangeValueTo']} {$lang['On']}!! ";
					}
				} else if (($row4['trigger_type'] == 2) && ($row4['trigger_state'] == 1)) {  //If trigger is 'Device has state On'
					if ($xmldata->state == 1) { 	// current state is On
						$take_action = true;
						$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$row4['name']} {$lang['DeviceIsOn']}";
						$mailMessage = "{$notifyType}: {$row4['name']} {$lang['HasValue']} {$lang['On']}!! ";
					}		
				} else if (($row4['trigger_type'] == 1) && ($row4['trigger_state'] == 2)) {  //If trigger is 'Device change to state Off'
					if (($xmldata->state == 2) && ($row4['device_state'] == 1)) { 	// current state is Off, last state was On
						$take_action = true;
						$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$row4['name']} {$lang['DeviceTurnedOff']}";
						$mailMessage = "{$notifyType}: {$row4['name']} {$lang['HasChangeValueTo']} {$lang['Off']}!! ";
					}		
				} else if (($row4['trigger_type'] == 2) && ($row4['trigger_state'] == 2)) {  //If trigger is 'Device has state Off'
					if ($xmldata->state == 2) { 	// current state is Off
						$take_action = true;
						$mailSubject = "{$config['pagetitle']}: {$notifyType}: {$row4['name']} {$lang['DeviceIsOff']}";
						$mailMessage = "{$notifyType}: {$row4['name']} {$lang['HasValue']} {$lang['Off']}!! ";
					}
				}

				echo "<br><br>Device name: " . $row4['name'];
				
				if ($take_action) {
					
					// Check for warning last sent
					$timeSinceLastWarning = (time() - $row4['last_warning']);

					// Check if notification-repeat-state
					if (($row4['repeat_alert'] * 60) < $timeSinceLastWarning) 
						$repeatDeviceNotification = true;
					else 
						$repeatDeviceNotification = false;

					echo "<br><b>Action taken!</b>";

					
					// Send mail
					if ($row4['send_to_mail'] == 1 && $repeatDeviceNotification == true) {
						$queryTimestamp = "UPDATE ".$db_prefix."schedule_device SET last_warning='".time()."' WHERE notification_id='".$row4['notification_id']."'";
						$resultTimestamp = $mysqli->query($queryTimestamp);			

						//$mailMessage = "{$notifyType}: {$lang['Schedule']}.";
							
						// Use mail-function in /lib/php_functions/global.functions.inc.php
						if (!empty($row4['notification_mail_primary'])) sendMail($row4['notification_mail_primary'], $mailSubject, $mailMessage);
						if (!empty($row4['notification_mail_secondary'])) sendMail($row4['notification_mail_secondary'], $mailSubject, $mailMessage);
					}

					// Send push
					if ($row4['send_push'] == 1 && !empty($telldusConf['push_user']) && !empty($telldusConf['push_app'])) {
						$pushMessage = "{$row4['push_message']}";
						//$mess = "({$row4['name']}){$row4['device_id']}";

						//$pushMessage = str_replace("%%device%%", $mess, $pushMessage);
						$pushTitle = "{$config['pagetitle']}: {$notifyType} {$lang['Schedule']}.";
						// Use PushOver curl
						sendPush("{$telldusConf['push_app']}", "{$telldusConf['push_user']}", "{$pushTitle}", "{$pushMessage}");
					}	

					if ($row4['action_device'] > 0) { //action defined
						$params = array('id'=> $row4['action_device']);							
						if ($row4['action_device_set_state'] == 0)
							$reply = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOff', $params, 'GET');
						else 
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOn', $params, 'GET');
					}
						
				} else {
					echo "<br>No action this time!";
				} //if (take_action)
			} //end-if
			
			$query = "UPDATE ".$db_prefix."schedule_device SET 
					device_state=".$xmldata->state." 
					WHERE device_id='".$row4['device_id']."'";
			$result = $mysqli->query($query);

		} //end-while
    } //end-numRows
	
	echo "<br><br>Schedule script completed";

    
?>