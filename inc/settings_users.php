<?php


	/* Check access
	--------------------------------------------------------------------------- */
	if ($user['admin'] != 1) {
			header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
			exit();
	}
	
	echo "<h3>".$lang['Users']."</h3>";


	echo "<div style='float:right; margin-top:-45px; margin-right:15px;'>";
		echo "<a class='btn btn-success' href='?page=settings&view=user&action=create'>".$lang['Create user']."</a>";
	echo "</div>";



	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>".$lang['User added']."</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-info'>".$lang['Userdata updated']."</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-info'>".$lang['User deleted']."</div>";
	}




	echo "<table class='table table-striped table-hover'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th>#</th>";
				echo "<th>".$lang['Email']."</th>";
				echo "<th>".$lang['Admin']."</th>";
				if ($config['log_activity'] != 0) echo "<th>".$lang['Last_active']."</th>";
				echo "<th></th>";
			echo "</tr>";
		echo "</thead>";
		
		echo "<tbody>";

			$query = "SELECT * FROM ".$db_prefix."users ORDER BY mail ASC";
		    $result = $mysqli->query($query);
		    while($row = $result->fetch_array()) {
				echo "<tr>";

					echo "<td>".$row['user_id']."</td>";
					echo "<td>".$row['mail']."</td>";
					
					echo "<td>";
						if ($row['admin'] == 1) echo "<img style='height:16px;' src='images/metro_black/check.png' alt='icon' />";
					echo "</td>";
					
					if ($config['log_activity'] != 0) {
						echo "<td>";
								if ($row['last_active'] != 0) echo date("Y-m-d H:i:s", $row['last_active']);
						echo "</td>";
					}

					echo "<td style='text-align:right;'>";
						if ($_SESSION['fuTelldus_user_loggedin'] != $row['user_id']) echo "<a class='btn btn-danger' href='?page=settings_exec&action=userDelete&id={$row['user_id']}' onclick=\"return confirm('".$lang['Are you sure you want to delete']."')\">".$lang['Delete']."</a>";
						echo "<a class='btn' href='?page=settings&view=user&action=edit&id={$row['user_id']}'>".$lang['Edit']."</a> &nbsp; ";
					echo "</td>";

				echo "</tr>";
			}


		echo "</tbody>";
	echo "</table>";

?>