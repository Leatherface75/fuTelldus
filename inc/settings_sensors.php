<?php
	
	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='getRequestToken.php'>your userprofile</a>.";
		exit();
	}



	/* Request sensors list from Telldus Live
	--------------------------------------------------------------------------- */
	if ($userTelldusConf['sync_from_telldus'] == 1) {
		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('includeIgnored' => '1');
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensors/list', $params, 'GET');

		/*
		echo '<pre>';
			echo( htmlentities($response->getBody()));
		echo '</pre>';
		*/
		

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);


		echo "<div class='hidden-phone' style='float:right; margin-right:25px; margin-bottom:-50px; color:green; font-size:10px;'>{$lang['List synced']}</div>";
	}






	/* Headline
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>".$lang['Sensors']."</h3>";





	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-warning'>".$lang['Sensor added to ignored']."</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-info'>".$lang['Sensor removed ignored']."</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-danger'>".$lang['Sensor removed']."</div>";
	}





	/* Sensors
	--------------------------------------------------------------------------- */
	if ($xmldata->sensor) {
	
	echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>".$lang['Name']."</th>";
					echo "<th>".$lang['Location']."</th>";
					echo "<th></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
			
		/* List sensors
		--------------------------------------------------------------------------- */
		foreach($xmldata->sensor as $sensorData) {
			
			$sensorID = trim($sensorData['id']);
			$name = trim($sensorData['name']);
			$lastUpdate = trim($sensorData['lastUpdated']);
			$ignored = trim($sensorData['ignored']);
			$client = trim($sensorData['client']);
			$clientName = trim($sensorData['clientName']);
			$online = trim($sensorData['online']);
			$editable = trim($sensorData['editable']);

			$monitorSensor = getField("monitoring", "".$db_prefix."sensors", "WHERE sensor_id='".$sensorData['id']."'");
			$publicValue = getField("public", "".$db_prefix."sensors", "WHERE sensor_id='".$sensorData['id']."'");
			if ($monitorSensor == NULL || $monitorSensor == "")
				$monitorSensor = 1;
			if ($publicValue == NULL || $publicValue == "")
				$publicValue = 0;			

					echo "<tr>";

						echo "<td>";
							echo "<a href='?page=sensors_data&id={$sensorID}'><span>#{$sensorID}: </span>{$name}</a>";
						echo "</td>";


						echo "<td class='hidden-phone'>{$clientName}</td>";

						echo "<td style='text-align:right;'>";

							if ($ignored == 1) {
								echo "<a class='hidden-phone btn btn-info' href='?page=sensors_exec&action=activateSensor&id={$sensorID}'>{$lang['Activate']}</a>";
								echo "<a class='visible-phone btn btn-info btn-small' href='?page=sensors_exec&action=activateSensor&id={$sensorID}'>{$lang['Activate']}</a>";
							}

							else {
								echo "<a class='hidden-phone btn btn-warning' href='?page=sensors_exec&action=ignoreSensor&id={$sensorID}'>{$lang['Ignore']}</a>";
								echo "<a class='visible-phone btn btn-warning btn-small' href='?page=sensors_exec&action=ignoreSensor&id={$sensorID}'>{$lang['Ignore']}</a>";
							}
							
							$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND sensor_id='{$sensorID}' LIMIT 1";
							$result = $mysqli->query($query);

							$row = $result->fetch_array();
							
							if ($row) {
								
							echo " &nbsp; ";

								echo "<a class='hidden-phone btn btn-danger' href='?page=sensors_exec&action=deleteSensor&id={$sensorID}' onclick=\"return confirm('".$lang['Are you sure you want to delete']."')\">".$lang['Delete']."</a>";
								echo "<a class='visible-phone btn btn-danger btn-small' href='?page=sensors_exec&action=deleteSensor&id={$sensorID}' onclick=\"return confirm('".$lang['Are you sure you want to delete']."')\">".$lang['Delete']."</a>";
								
							}

						echo "</td>";

					echo "</tr>";
		}

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
	
	}
	
	else echo "<div class='alert'>{$lang['Nothing to display']}</div>";
	
	
	
?>

