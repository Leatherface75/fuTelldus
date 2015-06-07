
<!--[if lt IE 9]><script src="../excanvas/excanvas.js"></script><![endif]-->

<script src="./lib/packages/jonthornton-jquery-timepicker-1.0.11-0-gc3f8ede/jquery.timepicker.min.js" ></script>
<link href="./lib/packages/jonthornton-jquery-timepicker-1.0.11-0-gc3f8ede/jquery.timepicker.css" rel="stylesheet">

<script>
	
	$(document).ready(function() {
		$('#dateFrom').datepicker({
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
		});
		
		$('#dateTo').datepicker({
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
		});


		$('#timeFrom').timepicker({ 'timeFormat': 'H:i' });
		$('#timeTo').timepicker({ 'timeFormat': 'H:i' });

		$('#tooltip').tooltip();
	});

</script>

<?php


	/* Get values
	--------------------------------------------------------------------------- */
	if (isset($_POST['submit'])) {
		$sensorID = clean($_GET['id']);

		$dateFrom = clean($_POST['dateFrom']);
		$timeFrom = clean($_POST['timeFrom']);

		$dateTo = clean($_POST['dateTo']);
		$timeTo = clean($_POST['timeTo']);

		//$jump = clean($_POST['jump']);

		header("Location: ?page=sensors&sensor=yes&id={$_GET["id"]}&name={$_GET["name"]}&dateFrom=$dateFrom&timeFrom=$timeFrom&dateTo=$dateTo&timeTo=$timeTo");
		exit();
	}


	if (isset($_GET['dateFrom'])) {
		//$sensorID = clean($_GET['id']);

		$dateFrom = clean($_GET['dateFrom']);
		$timeFrom = clean($_GET['timeFrom']);

		$dateTo = clean($_GET['dateTo']);
		$timeTo = clean($_GET['timeTo']);

		//$jump = clean($_GET['jump']);
		//if ($jump == 0) $jump = 1;


	} else {
		$dateFrom = date("Y-m-d", strtotime(' -1 day'));
		$timeFrom = date("H:i", strtotime(' -1 day'));

		$dateTo = date("Y-m-d");
		$timeTo = date("H:i");

		//$jump = 4;
	}



	// Create unix timestamps
	list($yearFrom, $monthFrom, $dayFrom) = explode("-", $dateFrom);
	list($hourFrom, $minFrom) = explode(":", $timeFrom);

	list($yearTo, $monthTo, $dayTo) = explode("-", $dateTo);
	list($hourTo, $minTo) = explode(":", $timeTo);

	$dateFrom = mktime($hourFrom, $minFrom, 00, $monthFrom, $dayFrom, $yearFrom);
	$dateTo = mktime($hourTo, $minTo, 00, $monthTo, $dayTo, $yearTo);




	/* Check for errors
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) {
		$error = false;

		if ($dateFrom > $dateTo) $error = true;
		if (date("d", $dateFrom) < 1 || date("d", $dateFrom) > 31) $error = true;
		if (date("d", $dateTo) < 1 || date("d", $dateTo) > 31) $error = true;
	}








	echo "<h4>".$lang['Report']."</h4>";



	/* Form
	--------------------------------------------------------------------------- */
	echo "<form action='?page=sensors&sensor=yes&id={$_GET["id"]}&name={$_GET["name"]}' method='POST'>";
		echo "<table width='100%'>";

			echo "<tr>";
				echo "<td>".$lang['Date from']."</td>";
				echo "<td>".$lang['Date to']."</td>";
			echo "</tr>";

			echo "<tr>";

				echo "<td>";
					echo "<input style='width:100px;' type='text' name='dateFrom' id='dateFrom' value='".date("Y-m-d", $dateFrom)."' />";
					echo "<input style='width:50px; margin-left:5px;' type='text' name='timeFrom' id='timeFrom' value='".date("H:i", $dateFrom)."' />";
				echo "</td>";

				echo "<td>";
					echo "<input style='width:100px;' type='text' name='dateTo' id='dateTo' value='".date("Y-m-d", $dateTo)."' />";
					echo "<input style='width:50px; margin-left:5px;' type='text' name='timeTo' id='timeTo' value='".date("H:i", $dateTo)."' />";
				echo "</td>";

				echo "<td><input class='btn btn-primary hidden-phone' type='submit' name='submit' value='".$lang['Show data']."' /></td>";
			echo "</tr>";
			echo "<tr><td class='visible-phone'>";
			echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Show data']."' />";
			echo "</td></tr>";

		echo "</table>";
	echo "</form>";














	if (isset($_GET['id']) && !$error) {

	        /* Max, min avrage
	        --------------------------------------------------------------------------- */
	        $queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='{$_GET['id']}' AND (time_updated > '$dateFrom' AND time_updated < '$dateTo') ORDER BY time_updated DESC LIMIT 1000";
	        $resultS = $mysqli->query($queryS);
	        $sensorData = $resultS->fetch_array();

	        echo "<div style='margin-top:40px;'>";
		        echo "<table class='table table-striped table-hover'>";
		            echo "<tbody>";


		                // Temperature
		                echo "<tr>";
		                    echo "<td>".$lang['Avrage']." ".strtolower($lang['Temperature'])."</td>";
		                    echo "<td>".round($sensorData['AVG(temp_value)'], 2)." &deg;</td>";
		                echo "</tr>";

		                echo "<tr>";
		                    echo "<td>".$lang['Max']." ".strtolower($lang['Temperature'])."</td>";
		                    echo "<td>".round($sensorData['MAX(temp_value)'], 2)." &deg; </td>";
		                echo "</tr>";

		                echo "<tr>";
		                    echo "<td>".$lang['Min']." ".strtolower($lang['Temperature'])."</td>";
		                    echo "<td>".round($sensorData['MIN(temp_value)'], 2)." &deg; </td>";
		                echo "</tr>";




		                // Humidity
		                if ($sensorData['AVG(humidity_value)'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Avrage']." ".strtolower($lang['Humidity'])."</td>";
		                        echo "<td>".round($sensorData['AVG(humidity_value)'], 2)." %</td>";
		                    echo "</tr>";
		                }

		                if ($sensorData['MAX(humidity_value)'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Max']." ".strtolower($lang['Humidity'])."</td>";
		                        echo "<td>".round($sensorData['MAX(humidity_value)'], 2)." %</td>";
		                    echo "</tr>";
		                }

		                if ($sensorData['MIN(humidity_value)'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Min']." ".strtolower($lang['Humidity'])."</td>";
		                        echo "<td>".round($sensorData['MIN(humidity_value)'], 2)." %</td>";
		                    echo "</tr>";
		                }



		            echo "</tbody>";
		        echo "</table>";
			echo "</div>";
		echo "</div>";
	}







	/* Show errormessage if error
	--------------------------------------------------------------------------- */
	if ($error) {
		echo "<div class='alert alert-error'>".$lang['Wrong timeformat']."</div>";	
	}


?>