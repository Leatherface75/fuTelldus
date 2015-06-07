<?php
	ob_start();
	session_start();

	require_once 'lib/base.inc.php';
	require_once 'HTTP/OAuth/Consumer.php';
	
	// Set public and private key to developers key
	$_SESSION['public'] = "FEHUVEW84RAFR5SP22RABURUPHAFRUNU";
	$_SESSION['private'] = "ZUXEVEGA9USTAZEWRETHAQUBUR69U6EF";

	$consumer = new HTTP_OAuth_Consumer($_SESSION['public'], $_SESSION['private']);

	$consumer->getRequestToken(constant('REQUEST_TOKEN'), constant('BASE_URL').'/getAccessToken.php');

	$_SESSION['token'] = $consumer->getToken();
	$_SESSION['tokenSecret'] = $consumer->getTokenSecret();

	$url = $consumer->getAuthorizeUrl(constant('AUTHORIZE_TOKEN'));
	header('Location:'.$url);

?>