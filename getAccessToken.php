<?php

ob_start();
session_start();

require_once 'lib/base.inc.php';
require_once 'HTTP/OAuth/Consumer.php';

$consumer = new HTTP_OAuth_Consumer($_SESSION['public'], $_SESSION['private'], $_SESSION['token'], $_SESSION['tokenSecret']);

try {
	$consumer->getAccessToken(constant('ACCESS_TOKEN'));
	
	$_SESSION['accessToken'] = $consumer->getToken();
	$_SESSION['accessTokenSecret'] = $consumer->getTokenSecret();
	
	// Update users teldus-config with recieved keys
	$query = "REPLACE INTO ".$db_prefix."users_telldus_config SET 
				user_id='".$user['user_id']."', 
				sync_from_telldus='0', 
				public_key='".$_SESSION['public']."', 
				private_key='".$_SESSION['private']."', 
				token='".$_SESSION['accessToken']."',  
				token_secret='".$_SESSION['accessTokenSecret']."'";
	$result = $mysqli->query($query);

	header("Location:index.php?page=settings&view=user&action=edit&id=".$user['user_id']);
} catch (Exception $e) {
	?>
		<p>Authorization failed!</p>
		<p><a href="index.php">Go back</a></p>
	<?php
}

?>
