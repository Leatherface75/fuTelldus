<?php

	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);





	/* add sensor
	--------------------------------------------------------------------------- */
	if ($action == "addSensor") {

		$query = "UPDATE ".$db_prefix."sensors SET monitoring='". 1 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=sensors&msg=01");
		exit();
	}


	/* delete sensor
	--------------------------------------------------------------------------- */
	if ($action == "deleteSensor") {
		
		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('id' => $getID, 'ignore' => '1');
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/setIgnore', $params, 'GET');

		/*
		echo '<pre>';
			echo( htmlentities($response->getBody()));
		echo '</pre>';
		*/
		

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);

		$query = "DELETE FROM ".$db_prefix."sensors WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=settings&view=sensors&msg=03");
		exit();
	}


	/* ignore sensor
	--------------------------------------------------------------------------- */
	if ($action == "ignoreSensor") {

		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('id' => $getID, 'ignore' => '1');
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/setIgnore', $params, 'GET');

		/*
		echo '<pre>';
			echo( htmlentities($response->getBody()));
		echo '</pre>';
		*/
		

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);

		// Redirect
		header("Location: ?page=settings&view=sensors&msg=01");
		exit();
	}
	
	
	/* activate sensor
	--------------------------------------------------------------------------- */
	if ($action == "activateSensor") {

		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('id' => $getID, 'ignore' => '0');
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/setIgnore', $params, 'GET');

		/*
		echo '<pre>';
			echo( htmlentities($response->getBody()));
		echo '</pre>';
		*/
		

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);

		// Redirect
		header("Location: ?page=settings&view=sensors&msg=02");
		exit();
	}


	/* remove sensor
	--------------------------------------------------------------------------- */
	if ($action == "removeSensor") {

		$query = "UPDATE ".$db_prefix."sensors SET monitoring='". 0 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=sensors&msg=02");
		exit();
	}



	/* Set public
	--------------------------------------------------------------------------- */
	if ($action == "setSensorPublic") {

		$query = "UPDATE ".$db_prefix."sensors SET public='". 1 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ".$_SERVER['HTTP_REFERER']."");
		exit();
	}

	/* Set non public
	--------------------------------------------------------------------------- */
	if ($action == "setSensorNonPublic") {

		$query = "UPDATE ".$db_prefix."sensors SET public='". 0 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ".$_SERVER['HTTP_REFERER']."");
		exit();
	}

?>