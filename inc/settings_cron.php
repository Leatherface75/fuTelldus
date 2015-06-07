<?php

	/* Check access
	--------------------------------------------------------------------------- */
	if ($user['admin'] != 1) {
			header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
			exit();
	}
	
?>


<h3>Cron files</h3>

<p>Run CRON files manually to get/check data. The link will open in a new window, and would most likely don't return any data/text in your browser</p>

<a href='cron_temp_log.php' target='_blank'>> Get temperature values</a><br />
<a href='cron_schedule.php' target='_blank'>> Run schedule</a>