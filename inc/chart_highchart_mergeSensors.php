<script src="lib/packages/Highstock-1.3.1/js/highstock.js"></script>
<script src="lib/packages/Highstock-1.3.1/js/modules/exporting.js"></script>

<?php

	// Set how long back you want to pull data
    $showFromDate = time() - 86400 * $config['chart_max_days']; // 86400 => 24 hours * 10 days










// Temperature chart
echo <<<end
<div class="well">
<script type="text/javascript">

$(function() {
	$('#container').highcharts('StockChart', {
		

		chart: {
		},

		title: {
            text: '{$lang["Temperature"]} (\u00B0C)'
        },

		rangeSelector: {
			enabled: true,
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
			}, {
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
			verticalAlign: "bottom"
		},

		

		series: [
end;
    /* TEMP SENSOR 01: Get sensors
    --------------------------------------------------------------------------- */
    $query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_array()) {


        unset($temp_values);
        $joinValues = "";


        /* Get sensordata and generate graph
        --------------------------------------------------------------------------- */
        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row["sensor_id"]}' AND time_updated > '$showFromDate' ORDER BY time_updated ASC";
        $resultS = $mysqli->query($queryS);

        
        while ($sensorData = $resultS->fetch_array()) {
            $db_tempValue = trim($sensorData["temp_value"]);

            $timeJS = $sensorData["time_updated"] * 1000;
            $temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";
        }


        $joinValues = join($temp_values, ',');
echo <<<end
                {
			name: '{$row["name"]}',
			data: [$joinValues],
			type: 'spline',
			tooltip: {
				valueDecimals: 1, valueSuffix: '\u00B0C'
			}
		},
end;
    }
echo <<<end
                
                ],

		tooltip: {
	        valueSuffix: '¡C'
	    },

		xAxis: {
			type: 'datetime',
		}, 

		yAxis: {
			title: {
				text: '{$lang["Temperature"]} (\u00B0C)',
			},
			labels: {
			    formatter: function() {
					return this.value +'\u00B0C';
			    },
			    style: {
					color: '#777'
			    }
			},
		}, 
	});
});

</script>

<div id="container" style="height: 500px; min-width: 240px"></div>
</div>
end;



// Humidity chart
echo <<<end
<div class="well">
<script type="text/javascript">

$(function() {
	$('#container2').highcharts('StockChart', {
		

		chart: {
		},

		title: {
            text: '{$lang["Humidity"]} (%)'
        },

		rangeSelector: {
			enabled: true,
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
			}, {
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
			verticalAlign: "bottom"
		},

		

		series: [
end;
    /* TEMP SENSOR 01: Get sensors
    --------------------------------------------------------------------------- */
    $query2 = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
    $result2 = $mysqli->query($query2);

    while ($row2 = $result2->fetch_array()) {


        unset($hum_values);
        $joinValues2 = "";


        /* Get sensordata and generate graph
        --------------------------------------------------------------------------- */
        $queryS2 = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row2["sensor_id"]}' AND time_updated > '$showFromDate' ORDER BY time_updated ASC";
        $resultS2 = $mysqli->query($queryS2);

        
        while ($sensorData2 = $resultS2->fetch_array()) {
            $db_humValue = trim($sensorData2["humidity_value"]);

            $timeJS2 = $sensorData2["time_updated"] * 1000;
            $hum_values[]        = "[" . $timeJS2 . "," . round($db_humValue, 2) . "]";
        }


        $joinValues2 = join($hum_values, ',');
	if ($db_humValue == 0) continue;
echo <<<end
                {
			name: '{$row2["name"]}',
			data: [$joinValues2],
			type: 'spline',
			tooltip: {
				valueDecimals: 1, valueSuffix: '%'
			}
		},
end;
    }
echo <<<end
                
                ],

		tooltip: {
	        valueSuffix: '%'
	    },

		xAxis: {
			type: 'datetime',
		}, 

		yAxis: {
			title: {
				text: '{$lang["Humidity"]} (%)',
			},
			labels: {
			    formatter: function() {
					return this.value +'%';
			    },
			    style: {
					color: '#777'
			    }
			},
		}, 
	});
});

</script>

<div id="container2" style="height: 500px; min-width: 240px"></div>
</div>
end;


?>
