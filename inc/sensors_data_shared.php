
<?php
	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);



	/* Max, min avrage
    --------------------------------------------------------------------------- */
    $sensorResults = $mysqli->query("SELECT * FROM ".$db_prefix."sensors_shared WHERE share_id='$getID' LIMIT 1");
    $row = $sensorResults->fetch_array();

		$urlCount = $row['url_counter'];
		$urlCount = $urlCount+1;
		$sensorUrl = str_replace("{counter}", $urlCount, $row['url']);
		$sensorUrl = str_replace("{rand}", mt_rand(), $sensorUrl);

  	$sensorWindGustMaxToday = "";
  	$sensorWindGust = "";
  	$sensorWindLatest = "";
  	$sensorAvg10min = "";
  	$sensorRainToday = "";
  	$sensorRainYesterday = "";
  	$sensorRainMonth = "";
  	$sensorRainYear = "";
  	$sensorTemp = "";
  	$sensorTempMaxToday = "";
  	$sensorTempMinToday = "";
  	$sensorTempWindChill = "";
		$sensorHumidity = "";

		
		if ($row['sensor_type']==1) { // simple json (result is serialized and defined json tag is only expected to be exist once in the json)
			
			//Update url counter
			$query = "UPDATE ".$db_prefix."sensors_shared SET 
						url_counter=".$urlCount." 
						WHERE share_id='".$row['share_id']."'";
			$tmp_result = $mysqli->query($query);
			
			//Call shared sensor
			$curl = curl_init($sensorUrl);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$json = curl_exec($curl);
			curl_close($curl);
			
			$jsonArr = json_decode(utf8_encode($json), true);
			$sensorName = "";
			$sensorLoc = "";
			$sensorTemp = $jsonArr['temp'];
			$sensorHumidity = $jsonArr['hum'] . "%";
			$sensorUpd = $jsonArr['date'];
		} else if ($row['sensor_type']==2) {  //weather-display.com software
			// list of sensors http://www.weather-display.com/index3.php (add clientraw.txt?{counter} to sensor url)

			//Update url counter
			$query = "UPDATE ".$db_prefix."sensors_shared SET 
						url_counter=".$urlCount." 
						WHERE share_id='".$row['share_id']."'";
			$tmp_result = $mysqli->query($query);

			$curl = curl_init($sensorUrl);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$json = curl_exec($curl);
			curl_close($curl);

			$wdArr = explode(" ", utf8_encode($json));
			$sensorName = ""; //$row['description'];
    	$sensorLoc = "";
			$sensorWindGustMaxToday = round($wdArr[71]*0.5144, 1). " m/s";
	    $sensorWindGust = round($wdArr[2]*0.5144, 1). " m/s";
	    $sensorWindLatest = round($wdArr[1]*0.5144, 1) . " m/s";
	    $sensorAvg10min = round($wdArr[158]*0.5144, 1) . " m/s";
	    $sensorRainToday = $wdArr[7] . " mm";
	    $sensorRainYesterday = $wdArr[19] . " mm";
	    $sensorRainMonth = $wdArr[8] . " mm";
	    $sensorRainYear = $wdArr[9] . " mm";
	    $sensorTemp = $wdArr[4] . "&deg;";
	    $sensorTempMaxToday = $wdArr[46] . "&deg;";
	    $sensorTempMinToday = $wdArr[47] . "&deg;";
	    $sensorTempWindChill = $wdArr[44] . "&deg;";
			$sensorHumidity = $wdArr[5] . "%";
			$sensorDate = explode("/", $wdArr[74]); // '17/08/2015'
			$lastUpdate = mktime($wdArr[29], $wdArr[30], $wdArr[31], $sensorDate[1], $sensorDate[0], $sensorDate[2]);
			$sensorUpd = date("H:i:s d-m-y", $lastUpdate) . " &nbsp; (" . ago($lastUpdate) . ")"; 
			
		} else { //From another fuTelldus site
			$xmlData = simplexml_load_file($sensorUrl);
			$sensorName = $xmlData->sensor->name;
    	$sensorLoc = $xmlData->sensor->location;
    	$sensorTemp = $xmlData->sensor->temp . "&deg;";
			$sensorHumidity = $xmlData->sensor->humidity . "%";
    	
			$lastUpdate = $xmlData->sensor->lastUpdate;
			$sensorUpd = date("H:i:s d-m-y", $lastUpdate) . " &nbsp; (" . ago($lastUpdate) . ")";
		}



    /* Headline
	--------------------------------------------------------------------------- */
	echo "<h3>{$lang['Sensor']}: {$row['description']}</h3>";



	/* Public
	--------------------------------------------------------------------------- */


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
						echo $sensorUpd;
					echo "</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Temperature']."</td>";
					echo "<td>".$sensorTemp."</td>";
				echo "</tr>";

				if ($sensorTempWindChill !== "") {				
					echo "<tr>";
						echo "<td>".$lang['WindChill']."</td>";
						echo "<td>".$sensorTempWindChill."</td>";
					echo "</tr>";
				}				
				if ($sensorWindLatest !== "") {				
					echo "<tr>";
						echo "<td>".$lang['WindNow']."</td>";
						echo "<td>".$sensorWindLatest."</td>";
					echo "</tr>";
				}

				if ($sensorAvg10min !== "") {				
					echo "<tr>";
						echo "<td>".$lang['WindAvg10Min']."</td>";
						echo "<td>".$sensorAvg10min."</td>";
					echo "</tr>";
				}
				if ($sensorWindGust !== "") {				
					echo "<tr>";
						echo "<td>".$lang['WindGustNow']."</td>";
						echo "<td>".$sensorWindGust."</td>";
					echo "</tr>";
				}

	
				if ($sensorRainToday !== "") {				
					echo "<tr>";
						echo "<td>".$lang['RainToday']."</td>";
						echo "<td>".$sensorRainToday."</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
	echo "</div>";

	

    echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<tbody>";
				echo "<tr>";
					echo "<td colspan=2 style='text-align: center;font-size:18px;background-color: #DFDFE6;'><b>{$lang['Stats']}</b></td>";
				echo "</tr>";


				if ($sensorTempMinToday !== "") {				
					echo "<tr>";
						echo "<td>".$lang['TempMin']."</td>";
						echo "<td>".$sensorTempMinToday."</td>";
					echo "</tr>";
				}
				if ($sensorTempMaxToday !== "") {				
					echo "<tr>";
						echo "<td>".$lang['TempMax']."</td>";
						echo "<td>".$sensorTempMaxToday."</td>";
					echo "</tr>";
				}
				if ($sensorWindGustMaxToday !== "") {				
					echo "<tr>";
						echo "<td>".$lang['WindGustToday']."</td>";
						echo "<td>".$sensorWindGustMaxToday."</td>";
					echo "</tr>";
				}
	
				if ($sensorRainYesterday !== "") {				
					echo "<tr>";
						echo "<td>".$lang['RainYesterday']."</td>";
						echo "<td>".$sensorRainYesterday."</td>";
					echo "</tr>";
				}
				if ($sensorRainMonth !== "") {				
					echo "<tr>";
						echo "<td>".$lang['RainMonth']."</td>";
						echo "<td>". $sensorRainMonth ."</td>";
					echo "</tr>";
				}
				if ($sensorRainYear !== "") {				
					echo "<tr>";
						echo "<td>".$lang['RainYear']."</td>";
						echo "<td>".$sensorRainYear."</td>";
					echo "</tr>";
				}
  	

			echo "</tbody>";
		echo "</table>";
	echo "</div>";





	?>