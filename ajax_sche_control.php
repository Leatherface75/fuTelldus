<?php
	
	require("lib/base.inc.php");
	require("lib/auth.php");


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);




	/* Connect and send to telldus live
	--------------------------------------------------------------------------- */
	require_once 'HTTP/OAuth/Consumer.php';


	$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));


	$params = array('id'=> $getID, 'active' => $action);
	$response = $consumer->sendRequest(constant('REQUEST_URI').'/scheduler/setJob', $params, 'GET');


?>