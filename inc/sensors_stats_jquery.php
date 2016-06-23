<?php
 
	/* Connect to database
	--------------------------------------------------------------------------- */
	require("../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");

	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['fromtime'])) $fromTime = clean($_GET['fromtime']);
	else $fromTime = 0;
	if (isset($_GET['totime'])) $toTime = clean($_GET['totime']);
	else $toTime = time();	
	if (isset($_GET['title'])) $title = clean($_GET['title']);
	else $title = "";	
	



			/* Max, min avrage last 12 hours
		--------------------------------------------------------------------------- */
	//	$fromtime = time() - $getPeriod;
		$queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' and time_updated>'$fromTime' and time_updated<'$toTime'";
		$resultS = $mysqli->query($queryS);
		$sensorData = $resultS->fetch_array();

			echo "<table class='table table-striped table-hover'>";
				echo "<tbody>";

					echo "<tr>";
						echo "<td colspan=6 style='text-align: center;font-size:18px;background-color: #DFDFE6;'><b>" . $title . "</b></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan=3><b>" . $lang['Temperature'] . "</b></td>";
						echo "<td colspan=3><b>" . $lang['Humidity'] . "</b></td>";
					echo "</tr>";					
					echo "<tr>";
						echo "<td>".$lang['Min']."</td>";
						echo "<td>".$lang['Avrage']."</td>";
						echo "<td>".$lang['Max']."</td>";
						echo "<td>".$lang['Min']."</td>";
						echo "<td>".$lang['Avrage']."</td>";
						echo "<td>".$lang['Max']."</td>";					
					echo "</tr>";
					echo "<tr>";
						echo "<td>".round($sensorData['MIN(temp_value)'], 1)." &deg; </td>";
						echo "<td>".round($sensorData['AVG(temp_value)'], 1)." &deg;</td>";
						echo "<td>".round($sensorData['MAX(temp_value)'], 1)." &deg; </td>";
						echo "<td>".round($sensorData['MIN(humidity_value)'], 0)." %</td>";
						echo "<td>".round($sensorData['AVG(humidity_value)'], 0)." %</td>";
						echo "<td>".round($sensorData['MAX(humidity_value)'], 0)." %</td>";
					echo "</tr>";
				echo "</tbody>";
			echo "</table>";

	?>
</div>