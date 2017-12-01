<?php

/*
 * Bootstrap
 */
require dirname(__DIR__) . '/config/bootstrap.php';

/*
 * Configure the thresholds for module daily failure tolerance
 */
Daytalytics\ProxyFailureMonitor::$moduleConfig = array(
	//set sepcific allowed failures per day
	'GoogleGetter 1.9' => array('threshold' => 15)//current accepted failues ~14/day(~100/wk)
);

/*
 * Analyze failures
 */
$result = Daytalytics\ProxyFailureMonitor::run($argv);

/*
 * If any failed over threshold, generate an email to task
 */
if ($failed = $result->failed()) {
	$message = "The following proxy(s) had failures detected over module thresholds:\n";
	foreach($failed as $proxy_id => $stats) {
		$message .= "Proxy ID: {$proxy_id}\n";
		foreach($stats as $module => $stat) {
			$message .= "\t{$module}: {$stat['errors']}\n";
		}
	}
	Daytalytics\Mailer::mail_monoriting_task("Proxy Failures", $message);
}

?>