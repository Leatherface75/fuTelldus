<?php
    
    if (!$telldusKeysSetup) {
    	echo "No keys for Telldus has been added... Keys can be added under <a href='getRequestToken.php'>your userprofile</a>.";
    	exit();
    }



    /* Get chart type
    --------------------------------------------------------------------------- */
    if (empty($user['chart_type'])) $chartType = "rgraph";
    else $chartType = $user['chart_type'];

    if (isset($_GET['charttype'])) {
        $chartType = clean($_GET['charttype']);
    }

    if (isset($_GET['view'])) {
        $view = clean($_GET['view']);
    }





    /* Headline
    --------------------------------------------------------------------------- */
    echo "<h3>{$lang['Chart']}</h3>";




    /* Toggle
    --------------------------------------------------------------------------- */
    echo "<div style='float:right; margin-top:-45px; margin-right:20px;' class='btn-group'>";

            echo "<a class='btn btn-inverse dropdown-toggle' data-toggle='dropdown' href='#' role='button'>";
                echo "{$lang['Chart_type']}";
                echo "<span class='caret'></span>";
            echo "</a>";

            echo "<ul class='dropdown-menu pull-right' role='menu'>";
		if (!isset($_GET['charttype']) && $user['chart_type'] == "highcharts") {
		    echo "<li><a href='?page=chart&charttype=highcharts'>Switch to Highcharts</a></li>";
		}
		
		elseif (!isset($_GET['charttype']) && $user['chart_type'] == "rgraph") {
		    echo "<li><a href='?page=chart&charttype=rgraph'>Switch to RGraph</a></li>";
		}
                
		elseif ($chartType == "rgraph") {
                    echo "<li><a href='?page=chart&charttype=highcharts'>Switch to Highcharts</a></li>";
                }

                elseif ($chartType == "highcharts") {
                    echo "<li><a href='?page=chart&charttype=rgraph'>Switch to RGraph</a></li>";
                }
		
                echo "<li><a href='?page=report'>{$lang['Report']} (RGraph)</a></li>";
                echo "<li><a href='?page=chart&view=mergeCharts'>{$lang['Combine charts']}</a></li>";
            echo "</ul>";
        echo "</div>";

    


    



    /* Include chart
    --------------------------------------------------------------------------- */
    if (!isset($_GET['view'])) {
        if (!isset($_GET['charttype']) && $user['chart_type'] == "highcharts") {
            include("inc/chart_highchart_mergeSensors.php");
        }
        elseif (!isset($_GET['charttype']) && $user['chart_type'] == "rgraph") {
            include("inc/chart_rgraph_mergeSensors.php");
        }
        elseif ($chartType == "rgraph") {
            include("inc/chart_rgraph.php");
        }

        elseif ($chartType == "highcharts") {
            include("inc/chart_highchart.php");
        }

        elseif ($chartType == "mergeCharts") {
            include("inc/chart_highchart_mergeSensors.php");
        }
	
        else {
            echo "Something went wrong.. Could'n determine chart to display. Try selecting chart in your userprofile.";
        }
    }


    else {
        if ($view == "mergeCharts" && $user['chart_type'] == "rgraph") {
            include("inc/chart_rgraph_mergeSensors.php");
        }
	elseif ($view == "mergeCharts" && $user['chart_type'] == "highcharts") {
            include("inc/chart_highchart_mergeSensors.php");
        }
	elseif ($view == "mergeCharts") {
            include("inc/chart_rgraph_mergeSensors.php");
        }
    }

?>