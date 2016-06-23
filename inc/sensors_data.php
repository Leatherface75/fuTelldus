
<?php
	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);



	/* Max, min avrage
    --------------------------------------------------------------------------- */
    //$querySensorData = "SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$getID'";
    $sensorResults = $mysqli->query("SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$getID' LIMIT 1");
    $db_sensor = $sensorResults->fetch_array();

/* Get sensor data from telldus live
    --------------------------------------------------------------------------- */
    require_once 'HTTP/OAuth/Consumer.php';
	$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

	$params = array('id'=> $getID);
	$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/info', $params, 'GET');

	$xmlString = $response->getBody();
	$xmldata = new SimpleXMLElement($xmlString);
	
	if ($db_sensor['monitoring']==1) {
	   $monitor=true;
    } else {
	   $monitor=false;	
    }



 /* JQuery
	--------------------------------------------------------------------------- */
	echo "<script type='text/javascript'>";


	echo "$(function(){";
	
	echo "$('#showStats').click(function() {";
	echo "	var statsUrl = \"inc\\\\sensors_stats_jquery.php?id=".$getID."&title=". urlencode($lang['Custom'])."&fromtime=\";";
	echo "  var curTime = " . time() . ";";
	echo "  var statsDays = $('#statDays').val();";
	echo "  var statsHours = $('#statHours').val();"; 
	echo "  var fromTime = curTime - (statsDays*86400) - (statsHours*3600);"; //string/int problem
	
	 
	echo "	statsUrl = statsUrl.concat(fromTime);";
	echo "	$('#divStats').load(statsUrl);";  
	echo "});";
	
	echo "$('#edit_user_details').find('select').trigger('change');";
	
	echo "$('.statsperiod').change(function(){";
	echo "  var data = $(this).val();";
	echo "  if (data == 'Custom') {";
	echo "    document.getElementById('customData').style.display = 'block';";
	echo "  } else {";
	echo "    document.getElementById('customData').style.display = 'none';";
	echo "    var statsUrl = \"inc\\\\sensors_stats_jquery.php?id=".$getID."&\";";
	echo "    statsUrl = statsUrl.concat(data);";
	echo "    $('#divStats').load(statsUrl);";          
	echo "  }";
	echo "});";
	echo "});";
	
	echo "</script>";

	
    /* Headline
	--------------------------------------------------------------------------- */
	echo "<h3>{$lang['Sensor']}: {$db_sensor['name']}</h3>";



	/* Public
	--------------------------------------------------------------------------- */
	echo "<div style='float:right; margin-top:-45px; margin-right:20px;'>";

		if ($db_sensor['public'] == 1) {
			echo "<a class='btn btn-success' href='?page=sensors_exec&action=setSensorNonPublic&id=$getID'>{$lang['Public']}</a>";
		}

		else {
			echo "<a class='btn btn-inverse' href='?page=sensors_exec&action=setSensorPublic&id=$getID'>{$lang['Non public']}</a>";
		}


	echo "</div>";


    echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<tbody>";
				echo "<tr>";
					echo "<td colspan=2 style='text-align: center;font-size:18px;background-color: #DFDFE6;'><b>{$lang['Current']}</b></td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Last update']."</td>";
					echo "<td>";
						$lastUpdate = trim($xmldata->lastUpdated);
						echo date("H:i:s d-m-y", $lastUpdate) . " &nbsp; (" . ago($lastUpdate) . ")";
					echo "</td>";
				echo "</tr>";


				echo "<tr>";
					echo "<td>".$lang['Temperature']."</td>";
					echo "<td>".$xmldata->data[0]['value']." &deg;</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Humidity']."</td>";
					echo "<td>".$xmldata->data[1]['value']." %</td>";
				echo "</tr>";

			echo "</tbody>";
		echo "</table>";
	echo "</div>";




	if ($monitor == true) {

		$startOfToday = strtotime("midnight");
		$startOfMonth = strtotime("first day of"); //http://php.net/manual/en/datetime.formats.relative.php
		$startOfLastMonth = strtotime("first day of", $startOfMonth-86400);
		$last12h = date('U') - 43200;
		$last24h = date('U') - 86400;

		echo "<table>";
		echo "<tbody>";
		echo "<tr>";
		
		echo "<td style='text-align: center;font-size:18px;'>";
		echo "<select class='statsperiod' id='edit_user_details'>";
		echo "	<option value='fromtime=". $startOfToday."&title=".urlencode($lang['Today'])."'>".$lang['Today']."</option>";
		echo "	<option value='fromtime=".$startOfMonth."&title=".urlencode($lang['ThisMonth'])."'>".$lang['ThisMonth']."</option>";
		echo "	<option value='fromtime=".$startOfLastMonth."&totime=".$startOfMonth."&title=".urlencode($lang['LastMonth'])."'>".$lang['LastMonth']."</option>";
		echo "	<option value='fromtime=".$last12h."&title=".urlencode($lang['Last12h'])."'>".$lang['Last12h']."</option>";
		echo "	<option value='fromtime=".$last24h."&title=".urlencode($lang['Last24h'])."'>".$lang['Last24h']."</option>";
		echo "	<option value='fromtime=0&title=".urlencode($lang['AllTime'])."'>".$lang['AllTime']."</option>";
		echo "  <option value='Custom'>".$lang['Custom']."</option>";
		echo "</select>";
		echo "</td>";
		
		echo "<td style='display: none; text-align: centre;font-size:11px;' id='customData'>";
		echo " &nbsp; &nbsp; Days: <input type='number' size=4 id='statDays' name='statDays' value=0 style='width:30px;'>";
		echo " &nbsp;&nbsp;Hours: <input type='number' size=4 id='statHours' name='statHours' value=0 style='width:30px;'>";
		echo " &nbsp;&nbsp;<input type='button' id='showStats' value = 'Visa' style='vertical-align: top'>";
		echo "</td>";
		
		echo "</tr>";
		echo "</tbody>";
		echo "</table>";

		/* Max, min average table
		--------------------------------------------------------------------------- */
		$fromtime = time() - (1 * 12 * 60 * 60);
		$queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' and time_updated>'$startOfToday'";
		$resultS = $mysqli->query($queryS);
		$sensorData = $resultS->fetch_array();

		echo "<div class='well' id='divStats'>";
		echo "<table class='table table-striped table-hover'>";
		echo "<tbody>";

		echo "<tr>";
		echo "<td colspan=6 style='text-align: center;font-size:18px;background-color: #DFDFE6;'><b>" . $lang['Today'] . "</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=3><b>" . $lang['Temperature'] . "</b></td>";
		echo "<td colspan=3><b>" . $lang['Humidity'] . "</b></td>";
		echo "</tr>";					
		
		echo "<tr>";
		echo "<td>".$lang['Min']."</td>";
		echo "<td>".$lang['Avrage']."</td>";
		echo "<td>".$lang['Max']."</td>";
		echo "<td>".$lang['Min']."</td>";
		echo "<td>".$lang['Avrage']."</td>";
		echo "<td>".$lang['Max']."</td>";					
		echo "</tr>";
		
		echo "<tr>";
		echo "<td>".round($sensorData['MIN(temp_value)'], 1)." &deg; </td>";
		echo "<td>".round($sensorData['AVG(temp_value)'], 1)." &deg;</td>";
		echo "<td>".round($sensorData['MAX(temp_value)'], 1)." &deg; </td>";
		echo "<td>".round($sensorData['MIN(humidity_value)'], 0)." %</td>";
		echo "<td>".round($sensorData['AVG(humidity_value)'], 0)." %</td>";
		echo "<td>".round($sensorData['MAX(humidity_value)'], 0)." %</td>";
		echo "</tr>";
		
		echo "</tbody>";
		echo "</table>";
		echo "</div>";
		
	} //	if ($monitor == true) {

		echo "<div class='well'>";

		require_once 'HTTP/OAuth/Consumer.php';
		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('id'=> $getID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/info', $params, 'GET');



		/* Get and extract the XML data
		--------------------------------------------------------------------------- */
		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);



		echo "<table class='table table-striped table-hover'>";
			echo "<tbody>";
				echo "<tr>";
					echo "<td colspan=2 style='text-align: center;font-size:18px;background-color: #DFDFE6;'><b>Sensor info</b></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>".$lang['ID']."</td>";
					echo "<td>".$xmldata->id."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Client name']."</td>";
					echo "<td>".$xmldata->clientName."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Name']."</td>";
					echo "<td>".$xmldata->name."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Last update']."</td>";
					echo "<td>";
						$lastUpdate = trim($xmldata->lastUpdated);
						echo date("H:i:s y-m-d", $lastUpdate) . " &nbsp; (" . ago($lastUpdate) . ")";
					echo "</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Ignored']."</td>";
					echo "<td>".$xmldata->ignored."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Editable']."</td>";
					echo "<td>".$xmldata->editable."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Temperature']."</td>";
					echo "<td>".$xmldata->data[0]['value']."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Humidity']."</td>";
					echo "<td>".$xmldata->data[1]['value']."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Protocol']."</td>";
					echo "<td>".$xmldata->protocol."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Sensor ID']."</td>";
					echo "<td>".$xmldata->sensorId."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Timezone offset']."</td>";
					echo "<td>".$xmldata->timezoneoffset."</td>";
				echo "</tr>";

			echo "</tbody>";
		echo "</table>";
	echo "</div>";


	if ($monitor == true) {
	
	echo "<div style='text-align:right;'>";
		echo "<a style='margin-right:15px;' target='_blank' href='./public/index.php?page=chart&id=$getID'>Public chart</a>";
		echo "<a style='margin-right:15px;' target='_blank' href='./public/xml_sensor.php?sensorID=$getID'>XML Latest values</a>";
		echo "<a style='margin-right:15px;' target='_blank' href='./public/xml_sensor_log.php?sensorID=$getID'>XML Values last day</a>";
	echo "</div>";


	echo "<h3>".  $lang['Latest readings'] . "</h3>";
	echo "<div class='well'>";
		$query = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' ORDER BY time_updated DESC LIMIT 20";
	    $result = $mysqli->query($query);


	    echo "<table class='table table-striped table-hover'>";
			
	    	echo "<thead>";
	    		echo "<tr>";
	    			//echo "<th>Sensor ID</th>";
	    			//echo "<th>Name</th>";
	    			echo "<th>".$lang['Time']."</th>";
	    			echo "<th>".$lang['Temperature']."</th>";
	    			echo "<th>".$lang['Humidity']."</th>";
	    		echo "</tr>";
	    	echo "</thead>";

			echo "<tbody>";
			    while ($row = $result->fetch_array()) {
			    	echo "<tr>";
			    		//echo "<td>{$row['sensor_id']}</td>";
			    		//echo "<td></td>";
			    		echo "<td>".date("H:i:s y-m-d", $row['time_updated'])." &nbsp; (".ago($row['time_updated']).")</td>";
			    		echo "<td>{$row['temp_value']} &deg;</td>";
			    		echo "<td>{$row['humidity_value']} %</td>";
			    	echo "</tr>";
			    }
			echo "<tbody>";

	   	echo "</table>";
			echo "</div>";
	} //	if ($monitor == true) {
	?>