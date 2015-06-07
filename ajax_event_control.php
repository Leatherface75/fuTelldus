<?php
	
	require("lib/base.inc.php");
	require("lib/auth.php");


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['description'])) $description = clean($_GET['description']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);
	
	
	
	/* Request event info from Telldus Live
	--------------------------------------------------------------------------- */
		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('id'=> $getID);

		$response = $consumer->sendRequest(constant('REQUEST_URI').'/event/info', $params, 'GET');

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);

	if ($xmldata->trigger) {
		
				/* Store eventtrigger data
				--------------------------------------------------------------------------- */
				foreach($xmldata->trigger as $eventData) {
					$triggerID = trim($eventData['id']);
					$eventID = $getID;
					$deviceID = trim($eventData['deviceId']);
					$method = trim($eventData['method']);
				}
	}


	/* Connect and send to telldus live
	--------------------------------------------------------------------------- */


	$consumer2 = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));


	$params2 = array('id'=> $getID, 'description' => $description, 'active' => $action);
	$response2 = $consumer2->sendRequest(constant('REQUEST_URI').'/event/setEvent', $params2, 'GET');
	
	
	$consumer3 = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));


	$params3 = array('id'=> $triggerID, 'eventId' => $eventID, 'deviceId' => $deviceID, 'method' => $method);
	$response3 = $consumer3->sendRequest(constant('REQUEST_URI').'/event/setDeviceTrigger', $params3, 'GET');


?>