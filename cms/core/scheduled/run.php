<?php
	require (dirname(__FILE__).'/../runtime/runtime.php');

	/* Check we're ok to run */

	if (!defined('PERCH_SCHEDULE_SECRET')) {
		die('You must set a secret. See http://docs.grabaperch.com/docs/scheduled-tasks/ for configuration instructions.');
	}

	if (isset($argv)) {
		$cli = true;

		if (!isset($argv[1])) {
			die('No secret found. See http://docs.grabaperch.com/docs/scheduled-tasks/ for configuration instructions.'."\n");
		}

		if ($argv[1]!=PERCH_SCHEDULE_SECRET) {
			die('Incorrect secret.'."\n");
		}

	}else{
		$cli = false;
		
		if (!isset($_GET['secret'])) {
			die('No secret found. See http://docs.grabaperch.com/docs/scheduled-tasks/ for configuration instructions.'."\n");
		}

		if ($_GET['secret']!=PERCH_SCHEDULE_SECRET) {
			die('Incorrect secret.'."\n");
		}
	}


	/* At this point, all should be good. */

	include(dirname(__FILE__).'/../lib/PerchScheduledTasks.class.php');
	include(dirname(__FILE__).'/../lib/PerchScheduledTask.class.php');

	$ScheduledTasks = new PerchScheduledTasks;

	// Try and install, if not installed already.
	if (!$ScheduledTasks->attempt_install()) {
		die ('Unable to create required database tables.');
	}


	// Run!
	$ScheduledTasks->run();



	
	//PerchUtil::output_debug();
?>