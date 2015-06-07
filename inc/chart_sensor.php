<script src="lib/packages/Highstock-1.3.1/js/highstock.js"></script>
<script src="lib/packages/Highstock-1.3.1/js/modules/exporting.js"></script>

<?php

   // Set how long back you want to pull data
    $showFromDate = time() - 86400 * $config['chart_max_days']; // 86400 => 24 hours * 10 days


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
        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$_GET['id']}' AND time_updated > '$showFromDate' ORDER BY time_updated ASC";
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
   $('#{$_GET["id"]}').highcharts('StockChart', {
      

      chart: { height: 600      // changed hight to make more space
      },

      title: {
            text: '{$_GET["name"]}'
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
            text: '{$lang["Temperature"]} (°C)',
	    style: {color: '#777'}
         },
         labels: {
             formatter: function() {
               return this.value + '\u00B0C';
             },
            format: '{value}°C',
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
            valueDecimals: 1, valueSuffix: '°C'
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
       //    valueSuffix: '°C'
       // },

      xAxis: {
         type: 'datetime',
      }, 

       
   });
});

</script>

<div id="{$_GET['id']}" style="height: 600px; min-width: 240px"></div>      
end;
   // changed height to match graf height

   echo "</div>";

//    }

?>