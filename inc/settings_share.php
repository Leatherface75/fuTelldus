<script>
   $(document).ready(function(){
      $('#title').click(function(){
         $('#tags_check').toggle();
      });
   })
</script>


<style type="text/css">
   #content {
      position: relative;
      padding: 10px;
   }


   #title {
    color: #fff;
    font-weight: bold;
    background-color: #538fbe;
    padding: 3px 3px;
    font-size: 12px;
    border: 1px solid #2d6898;
    top: -5px;
    left: 15px;
    z-index: 2;
    cursor: pointer;
	}

   #tags_check {
      border: 1px dotted grey;
      position: relative;
      z-index: 0;
      top: 3px;
      padding: 5px;	
   }
</style>

<?php
	
	echo "<h4>".$lang['Shared sensors']."</h4>";


 
	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>{$lang['Sensor added to monitoring']}</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success'>{$lang['Sensor removed from monitoring']}</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success'>{$lang['Data saved']}</div>";
	}



	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Add shared sensor']}</legend>";

		echo "<form action='?page=settings_exec&action=addSensorFromXML' method='POST'>";
			echo "<table width='100%'>";

				echo "<tr>";
					echo "<td>".$lang['Source']."</td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";

				echo "<tr>";
		   				echo "<td><select style='width:250px;' name='sensorSource'>";
		   				echo "<option value='0'>fuTelldus</option>";
		   				echo "<option value='2'>weather-display.com</option>";
		   			//	echo "<option value='1'>JSON</option>";
		   				echo "</select></td>";
					echo "<td></td>";
					echo "<td></td>";

				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Description']."</td>";
					echo "<td>".$lang['XML URL']."</td>";
					echo "<td></td>";
				echo "</tr>";
		   				
				echo "<tr>";

					echo "<td>";
						echo "<input style='width:180px;' type='text' name='description' id='description' value='' />";
					echo "</td>";

					echo "<td>";
						echo "<input style='width:360px;' type='text' name='xml_url' id='xml_url' value='' />";
					echo "</td>";

					echo "<td><input class='btn btn-primary' type='submit' name='submit' value='".$lang['Save data']."' /></td>";
				echo "</tr>";

			echo "</table>";
		echo "</form>";

	echo "</fieldset>";
	echo "<div id='content'>";
	echo "<div id='title'>Click for more info</div>";
  echo "<div id='tags_check' >";
   
	//	echo "<fieldset style='border: 1px dotted;visibility: hidden' id='test' ><br>";
	  	echo "<b>fuTelldus</b><br> Enter URL to the public sensor at any other fuTelldus site<br><br>";
	    echo "<b>weather-display.com:</b><br> Enter the url of any site using the weather-display software. Click on link for list of several sites using this<br>";
	    echo "<a href='http://www.weather-display.com/index3.php'>http://www.weather-display.com/index3.php</a><br>";
			echo "If you have problem to get it work:<br> 1. Make sure the url starts with http<br>  2. try to add clientraw.txt after url an see if you get any response.<br><br>";
	  
//	echo "</fieldset>";
	echo "</div>";
	echo "</div>";

	echo "<script>$('#tags_check').toggle();</script>";

	/* Shared sensors
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Sensors']}</legend>";

		$query = "SELECT * FROM ".$db_prefix."sensors_shared WHERE user_id='{$user['user_id']}' ORDER BY description ASC";
	    $result = $mysqli->query($query);
	    $numRows = $result->num_rows;

	    if ($numRows > 0) {


	    	while($row = $result->fetch_array()) {
				
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
		    	$sensorWindLatest = $wdArr[84];
		    	$sensorWindGustMaxToday = $wdArr[71];
  	    	$sensorAvgWind10min = $wdArr[158];
		    	$sensorRainToday = $wdArr[7];
		    	$sensorTemp = $wdArr[4] . "&deg;";
					$sensorHumidity = $wdArr[5] . "%";
					$sensorDate = explode("/", $wdArr[74]); // '17/08/2015'
					$unixTime = mktime($wdArr[29], $wdArr[30], $wdArr[31], $sensorDate[1], $sensorDate[0], $sensorDate[2]);
					$sensorUpd = ago($unixTime); 
					
				} else { //From another fuTelldus site
					$xmlData = simplexml_load_file($sensorUrl);
					$sensorName = $xmlData->sensor->name;
		    		$sensorLoc = $xmlData->sensor->location;
		    		$sensorTemp = $xmlData->sensor->temp . "&deg;";
					$sensorHumidity = $xmlData->sensor->humidity . "%";
					$sensorUpd = ago($xmlData->sensor->lastUpdate);
				}
		    	echo "<div style='border-bottom:1px solid #eaeaea; margin-left:15px; padding:10px;'>";


		    		// Tools
		    		echo "<div style='float:right;'>";

						echo "<div class='btn-group'>";

							if ($row['show_in_main'] == 1) $toggleClass = "btn-success";
							else $toggleClass = "btn-warning";

							if ($row['disable'] == 1) $toggleClass = "btn-danger";

							echo "<a class='btn dropdown-toggle $toggleClass' data-toggle='dropdown' href='#''>";
								echo "{$lang['Action']}";
								echo "<span class='caret'></span>";
							echo "</a>";

							echo "<ul class='dropdown-menu'>";
								if ($row['show_in_main'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=putOnMainSensorFromXML&id={$row['share_id']}'>Remove from main</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=putOnMainSensorFromXML&id={$row['share_id']}'>Put on main</a></li>";
				    			

				    			if ($row['disable'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=disableSensorFromXML&id={$row['share_id']}'>Enable</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=disableSensorFromXML&id={$row['share_id']}'>Disable</a></li>";
				    			

				    			echo "<li><a href='?page=settings_exec&action=deleteSensorFromXML&id={$row['share_id']}'>Delete</a></li>";
							echo "</ul>";
						echo "</div>";

		    		echo "</div>";



		    		echo "<div style='font-size:20px;'>".$row['description']."</div>";

		    		echo "<div style='font-size:11px;'>";
		    			echo "<b>{$lang['Sensorname']}:</b> ".$sensorName . "<br />";
		    			echo "<b>{$lang['Location']}:</b> ".$sensorLoc . "<br />";
		    			echo "<b>{$lang['XML URL']}:</b> <a href='{$row['url']}' target='_blank'>".$row['url']."</a>";
		    		echo "</div>";
		    		
		    		echo "<div style='display:inline-block; width:100px; margin:10px; font-size:20px;'>";
		    			echo "<img style='margin-right:10px;' src='images/therm.png' alt='icon' />";
		    			echo $sensorTemp;
		    		echo "</div>";

		    		if ($sensorHumidity > 0) {
			    		echo "<div style='display:inline-block; width:100px; margin:10px; font-size:20px;'>";
			    			echo "<img style='margin-right:10px;' src='images/water.png' alt='icon' />";
			    			echo $sensorHumidity;
			    		echo "</div>";
			    	}

		    		echo "<div style='font-size:10px'>";
		    			echo $sensorUpd;
		    		echo "</div>";

		    	echo "</div>";

		    }

		} else echo "<div class='alert'>{$lang['Nothing to display']}</div>";

	echo "</fieldset>";

?>
