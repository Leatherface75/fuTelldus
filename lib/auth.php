<?php
	
	// If user is not allready logged in	
	if (!isset($_SESSION['fuTelldus_user_loggedin'])) {

		// If Webapp id is set set loggedin user to webapp id
		if (isset($_GET['webapp'])) {
			$webappid = $_GET['webapp'];
			if (preg_match('/(http:\/\/|^\/|\.+?\/)/', $webappid)) {
				echo "Error";
				exit();
			}
			elseif (!empty($webappid)) {
				$query = "SELECT * FROM ".$db_prefix."users ORDER BY mail ASC";
				$result = $mysqli->query($query);
				while($row = $result->fetch_array()) {
				if ($webappid == hash("sha256", $row['user_id'])) {
				$_SESSION['fuTelldus_user_loggedin'] = $row['user_id'];
				header("Location: ./?webapp=".$webappid);
				exit();
				}
				}
			}
		}
		
		header("Location: ./login/");
		exit();	
		
	}
?>