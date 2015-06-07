<?php
	ob_start();
	session_start();

	unset($_SESSION['fuTelldus_user_loggedin']);

	setcookie("user_loggedin", "", time()-3600);

	header("Location: ../login/index.php?msg=02");
	exit();
?>