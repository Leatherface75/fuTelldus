<script src="lib/jscripts/futelldus_events.js"></script>
<script type="text/javascript">
	$(function() {
        $('.klicka').click(function() {
            var rowId = $(this).attr("row-id");
	    var rowDescription = $(this).attr("row-description");
	    var rowAction = $(this).attr("row-action");
            eventControl(rowAction, rowId, rowDescription);
        });
	
        $('.klicka2').click(function() {
            var rowId = $(this).attr("row-id");
	    var rowAction = $(this).attr("row-action");
            scheControl(rowAction, rowId);
        });
  });
</script>
<?php
	

	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='getRequestToken.php'>your userprofile</a>.";
		exit();
	}

	/* Request events list from Telldus Live
	--------------------------------------------------------------------------- */
	if ($userTelldusConf['sync_from_telldus'] == 1) {
		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		//$params = array();
		$params = array('listOnly'=> 1);
		//$params = array('id'=> $getID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/events/list', $params, 'GET');

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);
	}
		
	/* Request schedules list from Telldus Live
	--------------------------------------------------------------------------- */
	if ($userTelldusConf['sync_from_telldus'] == 1) {
		require_once 'HTTP/OAuth/Consumer.php';


		$consumer2 = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		//$params = array();
		$params2 = array();
		//$params = array('id'=> $getID);
		$response2 = $consumer2->sendRequest(constant('REQUEST_URI').'/scheduler/joblist', $params2, 'GET');

		$xmlString2 = $response2->getBody();
		$xmldata2 = new SimpleXMLElement($xmlString2);


		echo "<div class='hidden-phone' style='float:right; margin-right:25px; margin-bottom:-50px; color:green; font-size:10px;'>{$lang['List synced']}</div>";
	}




	/* List events
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>Telldus {$lang['Events']}</h3>";
	
	if ($xmldata->event) {

		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['ID']}</th>";
					echo "<th>{$lang['Name']}</th>";
					echo "<th>{$lang['Active']}</th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";

					
				/* Store event data
				--------------------------------------------------------------------------- */
				foreach($xmldata->event as $eventData) {
			
					$eventID = trim($eventData['id']);
					$description = trim($eventData['description']);
					$active = trim($eventData['active']);
					echo "<tr>";
					
						echo "<td class='hidden-phone'>#{$eventID}</td>";
		
						echo "<td>{$description}</td>";
						
						echo "<td class='hidden-phone' width='10%'>";
							if ($active == 1) echo "<img id='imgDisp{$eventID}' style='height:16px;' src='images/metro_black/check.png' alt='icon' class='klicka' row-action='0' row-description='{$description}' row-id='{$eventID}' />";
							if ($active == 0) echo "<img id='imgDisp{$eventID}' style='height:16px;' src='images/metro_black/cancel.png' alt='icon' class='klicka' row-action='1' row-description='{$description}' row-id='{$eventID}' />";
						echo "</td>";
						
						echo "<td class='visible-phone' width='20%'>";
						if ($active == 1) echo "<img id='imgDisp2{$eventID}' style='height:16px;' src='images/metro_black/check.png' alt='icon' class='klicka' row-action='0' row-description='{$description}' row-id='{$eventID}' />";
						if ($active == 0) echo "<img id='imgDisp2{$eventID}' style='height:16px;' src='images/metro_black/cancel.png' alt='icon' class='klicka' row-action='1' row-description='{$description}' row-id='{$eventID}' />";
						echo "</td>";

					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
		
	}
	
		else echo "<div class='alert'>{$lang['Nothing to display']}</div>";
		
		
	echo "<hr class='visible-phone'></hr>";



	/* List Schedules
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>Telldus {$lang['Schedules']}</h3>";
	
	if ($xmldata2->job) {

		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['ID']}</th>";
					echo "<th>{$lang['Device']}</th>";
					echo "<th>{$lang['Time']}</th>";
					echo "<th>{$lang['Weekdays']}</th>";
					echo "<th width='10%'>{$lang['Device action']}</th>";
					echo "<th width='10%'>{$lang['Active']}</th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
				/* Store schedule data
				--------------------------------------------------------------------------- */
				foreach($xmldata2->job as $scheduleData) {
			
					$scheduleID = trim($scheduleData['id']);
					$deviceID = trim($scheduleData['deviceId']);
					$method = trim($scheduleData['method']);
					$nextRun = trim($scheduleData['nextRunTime']);
					//$hour = trim($scheduleData['hour']);
					//$minute = trim($scheduleData['minute']);
					//if ($minute == "0") $minute = "00";
					$active2 = trim($scheduleData['active']);
					$weekdays = trim($scheduleData['weekdays']);

					$query = "SELECT * FROM ".$db_prefix."devices WHERE device_id='{$deviceID}' AND user_id='{$user['user_id']}' ORDER BY name ASC LIMIT 100";
					$result = $mysqli->query($query);
					$row = $result->fetch_array();

					echo "<tr>";
						echo "<td class='hidden-phone'>";
							echo "#{$scheduleID}";
						echo "</td>";

						echo "<td>{$row['name']}</td>";
						
						echo "<td>";
							//echo $hour.":".$minute."<br>";
							echo date("H:i", $nextRun) . "<br>";
							if ($method == 1) echo "<img class='visible-phone'  style='height:16px;' src='images/on.png' alt='icon' />";
							if ($method == 2) echo "<img class='visible-phone' style='height:16px;' src='images/off.png' alt='icon' />";
							if ($active2 == 1) echo "<img id='imgDisp3{$scheduleID}' style='height:16px;' src='images/metro_black/check.png' alt='icon' class='klicka2 visible-phone' row-action='0' row-id='{$scheduleID}' />";
							if ($active2 == 0) echo "<img id='imgDisp3{$scheduleID}' style='height:16px;' src='images/metro_black/cancel.png' alt='icon' class='klicka2 visible-phone' row-action='1' row-id='{$scheduleID}' />";
						echo "</td>";

						echo "<td>";
							if (strpos($weekdays, "1") !== false) $days[] = $lang['Monday'];
							if (strpos($weekdays, "2") !== false) $days[] = $lang['Tuesday'];
							if (strpos($weekdays, "3") !== false) $days[] = $lang['Wednesday'];
							if (strpos($weekdays, "4") !== false) $days[] = $lang['Thursday'];
							if (strpos($weekdays, "5") !== false) $days[] = $lang['Friday'];
							if (strpos($weekdays, "6") !== false) $days[] = $lang['Saturday'];
							if (strpos($weekdays, "7") !== false) $days[] = $lang['Sunday'];
							$i = 0;
							foreach($days as $day) {
							if ($i != (count($days) - 1)) echo $day.", ";
							else echo $day;
							$i++ ;
							}
							unset($days);
						echo "</td>";
						
						echo "<td class='hidden-phone'>";
							if ($method == 1) echo "<img style='height:30px;' src='images/on.png' alt='icon' />";
							if ($method == 2) echo "<img style='height:30px;' src='images/off.png' alt='icon' />";
						echo "</td>";
						
						echo "<td class='hidden-phone'>";
							if ($active2 == 1) echo "<img id='imgDisp4{$scheduleID}' style='height:16px;' src='images/metro_black/check.png' alt='icon' class='klicka2' row-action='0' row-id='{$scheduleID}' />";
							if ($active2 == 0) echo "<img id='imgDisp4{$scheduleID}' style='height:16px;' src='images/metro_black/cancel.png' alt='icon' class='klicka2' row-action='1' row-id='{$scheduleID}' />";
						echo "</td>";
						
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
		
	}
	
		else echo "<div class='alert'>{$lang['Nothing to display']}</div>";


?>