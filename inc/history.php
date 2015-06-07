

<?php
	

	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user&action=edit&id={$user['user_id']}'>your userprofile</a>.";
		exit();
	}
	
		if (isset($_GET['id'])) $getID = clean($_GET['id']); //Get id of sensor
		if (isset($_GET['name'])) $getName = clean($_GET['name']); //Get name of sensor



	/* Request device history from Telldus Live
	--------------------------------------------------------------------------- */
	if ($userTelldusConf['sync_from_telldus'] == 1) {
		require_once 'HTTP/OAuth/Consumer.php';
		
		$getFrom = strtotime('- 7 days'); //One week back
		$getTo = time(); //Until now
		
		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));


		$params = array('id'=> $getID, 'from' => $getFrom, 'to' => $getTo);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/history', $params, 'GET');
		

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);



		echo "<div class='hidden-phone' style='float:right; margin-right:25px; margin-bottom:-50px; color:green; font-size:10px;'>{$lang['List synced']}</div>";
	}




	/* List history
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>{$lang['Device History']}: (#{$getID}){$getName}</h3>";
	
	if ($xmldata->history) {

		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['Time']}</th>";
					echo "<th width='20%' style='text-align:left;'>{$lang['State']}</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			
			foreach($xmldata->history as $stringArray) $xmlArray[] = $stringArray;
			
			$xmlArray = array_reverse($xmlArray);
				
				foreach($xmlArray as $deviceHistory) {
					$time = intval($deviceHistory['ts']);
					echo "<tr valign='top'>";
						echo "<td>";
							echo date("Y-m-d H:i:s", $time);
						echo "</td>";

						echo "<td style='text-align:left;'>";

							if ($userTelldusConf['sync_from_telldus'] == 1) {
								if ($deviceHistory['state'] == 1) {
									echo "<img style='height:30px;' src='images/on.png' alt='icon' />";
								}
								elseif ($deviceHistory['state'] == 2) {
									echo "<img style='height:30px;' src='images/off.png' alt='icon' />";
								}
							}
						echo "</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
		
	}
	
	else echo "<div class='alert'>{$lang['Nothing to display']}</div>";



?>