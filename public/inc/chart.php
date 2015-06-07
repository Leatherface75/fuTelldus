<script src="../lib/packages/Highstock-1.3.1/js/highstock.js"></script>
<script src="../lib/packages/Highstock-1.3.1/js/modules/exporting.js"></script>
<script>

	jQuery(document).ready(function() {
	  jQuery("abbr.timeago").timeago();
	});

</script>

	<div class="container">

		<?php
			

			/* Get/set parameters
			--------------------------------------------------------------------------- */
			if (isset($_GET['id'])) {
				$getID = clean($_GET['id']);
			} else {
				echo "<p>Sensor ID is missing...</p>";
				exit();
			}

			$showFromDate = time() - 86400;






			/* Get sensor data
		    --------------------------------------------------------------------------- */
		    $query = "SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$getID'";
		    $result = $mysqli->query($query);
			$row = $result->fetch_array();


	        $sensorID = trim($row['sensor_id']);
	        echo "<h4>{$row['name']}</h4>";
	        echo "<h5 style='margin-left:10px;'>{$row['clientname']}</h5>";



    /* TEMP SENSOR 01: Get sensors
    --------------------------------------------------------------------------- */
//    $query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
//    $result = $mysqli->query($query);

//    while ($row = $result->fetch_array()) {

       echo "<div class='well'>";

        unset($temp_values);
        $joinValues = "";
        unset($hum_values);      // added humidity variables
        $humValues = "";      // added humidity variables


        /* Get sensordata and generate graph
        --------------------------------------------------------------------------- */
        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$getID}' AND time_updated > '$showFromDate' ORDER BY time_updated ASC";
        $resultS = $mysqli->query($queryS);

        
        while ($sensorData = $resultS->fetch_array()) {
            $db_tempValue = trim($sensorData["temp_value"]);
         $db_humValue = trim($sensorData["humidity_value"]);      //retrive humidity values

            $timeJS = $sensorData["time_updated"] * 1000;
            $temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";
	    $hum_values[]         = "[" . $timeJS . "," . round($db_humValue, 2) . "]";      // do something with values
        }


        $joinValues = join($temp_values, ',');
        $joinhumValues = join($hum_values, ',');      // do something more with values
	if ($db_humValue == 0) unset($joinhumValues);

echo <<<end
<script type="text/javascript">

$(function() {
   $('#{$getID}').highcharts('StockChart', {
      

      chart: { height: 600      // changed hight to make more space
      },


      rangeSelector: {
         enabled: false,
         buttons: [{
            type: 'hour',
            count: 1,
            text: '1h'
         },{
            type: 'hour',
            count: 12,
            text: '12h'
         },{
            type: 'day',
            count: 1,
            text: '1d'
         },{
            type: 'week',
            count: 1,
            text: '1w'
         }, {
            type: 'month',
            count: 1,
            text: '1m'
         }, {
            type: 'month',
            count: 6,
            text: '6m'
         }, {
            type: 'year',
            count: 1,
            text: '1y'
         }, {
            type: 'all',
            text: 'All'
         }],
         selected: 2
      },


        legend: {
         align: "center",
         layout: "horizontal",
         enabled: true,
         verticalAlign: "bottom",
      },

      yAxis: [{
         title: {
            text: '{$lang["Temperature"]} (\u00B0C)',
	    style: {color: '#777'}
         },
         labels: {
             formatter: function() {
               return this.value + '\u00B0C';
             },
            format: '{value}¡C',
             style: {
               color: '#777'
             }
         },
         height: 260,         // set manual hight for temperature
      }, {
         title: {                  // added humidity yAxis
            text: '{$lang["Humidity"]} (%)',
            style: {color: '#777'}   // set manual color for yAxis humidity
         },
         labels: {                  
             formatter: function() {
               return this.value +'%';
             },
            format: '{value}%',
             style: {
               color: '#777'
             }
         },
         top: 335,                  //positioned humidity below temperature 
         height: 120,               // set manual hight for humidity
         offset: 0,                  //lining humidity yAxis label upto temperature
          
      }],
	chart:{
	    type: 'area'
	},
      series: [{
         name: '{$lang["Temperature"]}',
         data: [$joinValues],
	 fillOpacity: '0.1',
	 color: '#00CC66',
         type: 'spline',
         tooltip: {
            valueDecimals: 1, valueSuffix: '\u00B0C'
         }
      },{
         name: '{$lang["Humidity"]}',            // added humidity as xAxis data
         data: [$joinhumValues],
	 fillOpacity: '0.1',
         color: '#0099FF',            // set graf color to the same as yAxis label
         type: 'spline',
         yAxis: 1,                  // connecting humdity data to yAxis
         tooltip: {
           valueDecimals: 1, valueSuffix: '%'
         }
         
      }],

      // tooltip: {                  // seperate tooltips set i data series
       //    valueSuffix: '¡C'
       // },

      xAxis: {
         type: 'datetime',
      }, 

       
   });
});

</script>

<div id="{$getID}" style="height: 600px; min-width: 240px"></div>      
end;
   // changed height to match graf height

   echo "</div>";

//    }





		        /* Max, min avrage
		        --------------------------------------------------------------------------- */
		        echo "<h5>".$lang['Total']."</h5>";

		        $queryNow = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' AND time_updated > '$showFromDate' ORDER BY time_updated DESC LIMIT 1";
		        $resultNow = $mysqli->query($queryNow);
		        $sensorDataNow = $resultNow->fetch_array();

		        $queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' AND time_updated > '$showFromDate'";
		        $resultS = $mysqli->query($queryS);
		        $sensorData = $resultS->fetch_array();


		        echo "<table class='table table-striped table-hover'>";
		            echo "<tbody>";


		                // Temperature
		            	 echo "<tr>";
		                    echo "<td>".$lang['Temperature']." ".strtolower($lang['Now'])."</td>";
		                    echo "<td>";
		                    	echo round($sensorDataNow['temp_value'], 2)." &deg;";
		                    	echo "<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("Y-m-d H:i", $sensorDataNow['time_updated'])."</abbr>";
		                    echo "</td>";
		                echo "</tr>";


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
		                if ($sensorDataNow['humidity_value'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Humidity']." ".strtolower($lang['Now'])."</td>";
		                        echo "<td>";
		                        	echo round($sensorDataNow['humidity_value'], 2)." %";
		                        	echo "<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("Y-m-d H:i", $sensorDataNow['time_updated'])."</abbr>";
		                        echo "</td>";
		                    echo "</tr>";
		                }


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

		?>

	</div>