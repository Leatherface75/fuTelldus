<?php
	require("lib/base.inc.php");
	require("lib/auth.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <title>Telldus db update</title>
</head>
<body>
	Database updates starts!<br /><br />
<?php
		
		/* GUIDELINES for database updates!
		
			Make sure that this script can be run over and over again by checking if value, column, table, etc exists before doing the change!!!
			Please also mark where your changes start with a date for change and if possible version of futelldus
			
		*/

		 
// -- CHANGES by androidemil 2015-08-12 Start --
		//Add column notification_type in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'notification_type'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD notification_type smallint(6) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add notification_type column in table futelldus_schedule_device: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule_device SET notification_type = 2';
			$result = $mysqli->query($query);	
		}

		//Add column sensor_type in table sensors_shared if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."sensors_shared LIKE 'sensor_type'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'sensors_shared ADD sensor_type smallint(6) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add sensor_type column in table futelldus_sensors_shared: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'sensors_shared SET sensor_type = 0';
			$result = $mysqli->query($query);	
		}

		//Add column temp_tag in table sensors_shared if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."sensors_shared LIKE 'temp_tag'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'sensors_shared ADD temp_tag varchar(256) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add temp_tag column in table futelldus_sensors_shared: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'sensors_shared SET temp_tag = ""';
			$result = $mysqli->query($query);	
		}

		//Add column humidity_tag in table sensors_shared if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."sensors_shared LIKE 'humidity_tag'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'sensors_shared ADD humidity_tag varchar(256) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add humidity_tag column in table futelldus_sensors_shared: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'sensors_shared SET humidity_tag = ""';
			$result = $mysqli->query($query);	
		}
		
		//Add column wind_tag in table sensors_shared if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."sensors_shared LIKE 'wind_tag'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'sensors_shared ADD wind_tag varchar(256) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add wind_tag column in table futelldus_sensors_shared: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'sensors_shared SET wind_tag = ""';
			$result = $mysqli->query($query);	
		}

		//Add column windgust_tag in table sensors_shared if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."sensors_shared LIKE 'windgust_tag'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'sensors_shared ADD windgust_tag varchar(256) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add windgust_tag column in table futelldus_sensors_shared: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'sensors_shared SET windgust_tag = ""';
			$result = $mysqli->query($query);	
		}

		//Add column raintoday_tag in table sensors_shared if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."sensors_shared LIKE 'raintoday_tag'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'sensors_shared ADD raintoday_tag varchar(256) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add raintoday_tag column in table futelldus_sensors_shared: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'sensors_shared SET raintoday_tag = ""';
			$result = $mysqli->query($query);	
		}

		//Add column url_counter in table sensors_shared if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."sensors_shared LIKE 'url_counter'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'sensors_shared ADD url_counter bigint(20) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add url_counter column in table futelldus_sensors_shared: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'sensors_shared SET url_counter = 1';
			$result = $mysqli->query($query);	
		}
	
		//Add column notification_type in table schedule if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule LIKE 'notification_type'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule ADD notification_type smallint(6) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add notification_type column in table futelldus_schedule: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule SET notification_type = 2';
			$result = $mysqli->query($query);	
		}
		
		  
// -- CHANGES by androidemil 2015-08-07 Start --
		//Add column last_warning in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'last_warning'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD last_warning int(11) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add last_warning column: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule_device SET last_warning = 0';
			$result = $mysqli->query($query);	
		}


// -- CHANGES by androidemil 2015-08-07 Start --

		//Add navbar layout configuration in config table
		$result = $mysqli->query("SELECT * FROM ".$db_prefix."config");
		while ($row = $result->fetch_array()) {
			$config[$row['config_name']] = $row['config_value'];
		}
		if (!isset($config['navbar_layout'])) {
				$query = "INSERT INTO ".$db_prefix."config SET 
					config_name='navbar_layout', 
					config_value='blue', 
					comment=''";
				$result = $mysqli->query($query);
				echo "<p>" . $result . "</p>";				
		}
	
		
		//Add column device in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'device_state'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD device_state tinyint(4) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add device_state column: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule_device SET device_state = 0';
			$result = $mysqli->query($query);	
		}
		
		//Add column device in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'action_device'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD action_device int(11) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add action_device column: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule_device SET action_device = 0';
			$result = $mysqli->query($query);	
		}
		
		//Add column action_device_set_state in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'action_device_set_state'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD action_device_set_state tinyint(4) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add action_device_set_state column: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule_device SET action_device_set_state = 0';
			$result = $mysqli->query($query);				
		}
		
		//Add column trigger_type in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'trigger_type'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD trigger_type tinyint(4) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add trigger_type column: " . $result . "</p>";	
			$query = 'UPDATE '.$db_prefix.'schedule_device SET trigger_type = 1';
			$result = $mysqli->query($query);	
		}
		
		//Add column trigger_state in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'trigger_state'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD trigger_state tinyint(4) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add trigger_state column: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule_device SET trigger_state = 1';
			$result = $mysqli->query($query);	
		}
		
		//Add column repeat_alert in table schedule_device if it doesn't exist already
		$result = $mysqli->query("SHOW COLUMNS FROM ".$db_prefix."schedule_device LIKE 'repeat_alert'");
		$exists = (mysqli_num_rows($result))?TRUE:FALSE;
		if($exists == FALSE)
		{
			$query = 'ALTER TABLE '.$db_prefix.'schedule_device ADD repeat_alert  smallint(6) NOT NULL';
			$result = $mysqli->query($query);
			echo "<p>Add repeat_alert column: " . $result . "</p>";
			$query = 'UPDATE '.$db_prefix.'schedule_device SET repeat_alert = 30';
			$result = $mysqli->query($query);	
		}
	// -- CHANGES by androidemil 2015-07-25 END --
?>

Database updates completed!

</body>
</html>