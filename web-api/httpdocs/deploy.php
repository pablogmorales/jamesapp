<?php
/**
 * Deploy webhook receiver
 */

/*
 * load app files, config etc... [optional]
 */
require_once dirname(__DIR__) . "/config/bootstrap.php";

if (!defined('DDM_COMMON')) {
	define('DDM_COMMON', '/var/www/common/');
}

/*
 * load the deploy libs [required]
 */
require DDM_COMMON . 'deployment/Deploy/deploy.php';

/*
 * set up the deploy hanlder [required]
 */
$deploy = new Ddm\Deploy\DeployHandler(new Ddm\Deploy\Service\Beanstalk());

/*
 * set notifications [required]
 */
$NEW_RELIC_API_KEY = getenv('NEW_RELIC_API_KEY');
$NEW_RELIC_APP_NAME = getenv('NEW_RELIC_APP_NAME') ?: ini_get('newrelic.appname');
if ($NEW_RELIC_API_KEY && $NEW_RELIC_APP_NAME):
	//1. New Relic
	$deploy->addNotification(new Ddm\Deploy\Notification\NewRelic($NEW_RELIC_API_KEY, $NEW_RELIC_APP_NAME));
endif;

/*
 * send the notifications [required]
 */
$deploy->notify();