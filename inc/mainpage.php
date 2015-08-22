<script>

	jQuery(document).ready(function() {
	  jQuery("abbr.timeago").timeago();
	});

</script>


<style>

	.sensors-wrap {
		padding-left:20px;
	}

	.sensors-wrap .sensor-blocks {
		display:inline-block;
		valign:top;
		margin-right:40px;
		margin-bottom:20px;
		min-width:200px;
		border:0px solid red;
	}

	.sensors-wrap .sensor-blocks img {
		margin-right:10px;
	}

	.sensors-wrap .sensor-name {
		font-size:14px; margin-bottom:0px; font-weight:bold;
	}

	.sensors-wrap .sensor-location {
		font-size:12px; margin-bottom:0px; font-weight:normal;
	}

	.sensors-wrap .sensor-location img {
		height:10px !important;
		margin-left:5px !important;
		margin-right:5px !important;
	}

	.sensors-wrap .sensor-temperature {
		font-size:40px; display:inline-block; valign:top; margin-left:15px; margin-top:6px; margin-bottom:6px; padding-top:10px; border:0px solid red;
	}

	.sensors-wrap .sensor-humidity {
		font-size:20px; display:inline-block; valign:top; margin-left:15px; padding-top:10px; border:0px solid red;
	}

	.sensors-wrap .sensor-timeago {
		font-size:10px; color:#777; text-align: center; padding-top: 8px;
	}

</style>



<?php

	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='getRequestToken.php'>your userprofile</a>.";
		exit();
	}


	// Margin for desktop and pad
	echo "<div style='height:30px;' class='hidden-phone'></div>";




	// Sensors
	echo "<div class='sensors-wrap'>";
	

		/* My sensors
   		--------------------------------------------------------------------------- */
		$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
	    $result = $mysqli->query($query);

	    while ($row = $result->fetch_array()) {
	    	

	    	$sensorID = trim($row['sensor_id']);

	    	$queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND time_updated > '$showFromDate' ORDER BY time_updated DESC LIMIT 1";
            $resultS = $mysqli->query($queryS);
            $sensorData = $resultS->fetch_array();


            echo "<div class='sensor-blocks well' onclick=\"location.href='?page=sensors_data&id=$sensorID';\" style='cursor:pointer;'>";

            	echo "<div class='sensor-name'>";
            		echo "{$row['name']}";
            	echo "</div>";

            	echo "<div class='sensor-location'>";
            		echo "<img src='images/location.png' alt='icon' />";
            		echo "{$row['clientname']}";
            	echo "</div>";

            	echo "<div class='sensor-temperature'>";
            		echo "<img src='images/therm.png' alt='icon' />";
            		echo "{$sensorData['temp_value']}&deg;";
            	echo "</div>";

            	if ($sensorData['humidity_value'] > 0) {
            		echo "<div class='sensor-humidity'>";
	            		echo "<img src='images/water.png' alt='icon' />";
	            		echo "{$sensorData['humidity_value']}%";
	            	echo "</div>";
            	}

            	echo "<div class='sensor-timeago'>";
            		echo "<abbr class=\"timeago\" title='".date("c", $sensorData['time_updated'])."'>".date("Y-m-d H:i", $sensorData['time_updated'])."</abbr>";
            	echo "</div>";

            echo "</div>";
	    }





	    /* Shared sensors
    	--------------------------------------------------------------------------- */
	    echo "<div>";
		    $query = "SELECT * FROM ".$db_prefix."sensors_shared WHERE user_id='{$user['user_id']}' AND show_in_main='1' ORDER BY description ASC";
		    $result = $mysqli->query($query);
		    $numRows = $result->num_rows;

		    if ($numRows > 0) {
		    	echo "<h4 style='margin-top:40px;'>{$lang['Shared sensors']}</h4>";

		    	while($row = $result->fetch_array()) {
						$sensorID = trim($row['share_id']);
						
						$urlCount = $row['url_counter'];
						$urlCount = $urlCount+1;
						$sensorUrl = str_replace("{counter}", $urlCount, $row['url']);
						$sensorUrl = str_replace("{rand}", mt_rand(), $sensorUrl);
						
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
							$sensorName = ""; //$row['description'];
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
					    	$sensorWindLatest = $wdArr[84];
					    	$sensorWindGustMaxToday = $wdArr[71];
					    	$sensorAvgWind10min = $wdArr[158];
					    	$sensorRainToday = $wdArr[7];
					    	
					    	$sensorTemp = $wdArr[4] . "&deg;";
								$sensorHumidity = $wdArr[5] . "%";
								$sensorDate = explode("/", $wdArr[74]); // '17/08/2015'
								$unixTime = mktime($wdArr[29], $wdArr[30], $wdArr[31], $sensorDate[1], $sensorDate[0], $sensorDate[2]);
								$sensorUpd = "<abbr class=\"timeago\" title='".date("c", $unixTime)."'>".date("Y-m-d H:i", $unixTime)."</abbr>";
							
						} else { //From another fuTelldus site
								$xmlData = simplexml_load_file($sensorUrl);
								$sensorName = $xmlData->sensor->name;
					    	$sensorLoc = $xmlData->sensor->location;
					    	$sensorTemp = $xmlData->sensor->temp . "&deg;C";
								$sensorHumidity = $xmlData->sensor->humidity . "%";
								$sensorUpd = "<abbr class=\"timeago\" title='".date("c", trim($xmlData->sensor->lastUpdate))."'>".date("Y-m-d H:i", trim($xmlData->sensor->lastUpdate))."</abbr>";
						}
				
			    
									echo "<div class='sensor-blocks well' onclick=\"location.href='?page=sensors_data_shared&id=". $row['share_id']."';\" style='cursor:pointer;'>";

		            	echo "<div class='sensor-name'>";
		            		echo $row['description'];
		            	echo "</div>";
									
									if ($sensorName != "") {
			            	echo "<div class='sensor-location'>";
			            		echo "<img src='images/location.png' alt='icon' />";
			            		echo $sensorLocation . " (".$sensorName.")";
			            	echo "</div>";
									}
									
		            	echo "<div class='sensor-temperature'>";
		            		echo "<img src='images/therm.png' alt='icon' />";
		            		echo $sensorTemp;
		            	echo "</div>";

		            	if ($sensorHumidity > 0) {
		            		echo "<div class='sensor-humidity'>";
			            		echo "<img src='images/water.png' alt='icon' />";
			            		echo $sensorHumidity;
			            	echo "</div>";
		            	}

		            	echo "<div class='sensor-timeago'>";
		            	echo $sensorUpd;
		            	echo "</div>";

		            echo "</div>";
			    }
			}
		echo "</div>";
	echo "</div>";

?>