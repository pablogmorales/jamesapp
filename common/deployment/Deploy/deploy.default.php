<?php
/**
 * Copy this into a web accessable location within your project and modify as required!
 *
 * Set the callback in your deployment service i.e. Beanstalk Post-deploy web hooks
 *
 * to http://hostname.com/deploy.php
 *
 * The below example is taken from Affilorama
 *  - service: Beanstalk
 *  - notify: New Relic
 *  - notify: Affilorama cms cleanup
 *
 */

/*
 * load app files, config etc... [optional]
 */
require dirname(__DIR__) . '/environment.php';
require __DIR__ . '/paths.php';

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

//1. New Relic
$newRelicKey = 'new-relic-api-key';
$newRelicApp = 'new-relic-app-name';
$deploy->addNotification(new Ddm\Deploy\Notification\NewRelic($newRelicKey, $newRelicApp));

//2. Cms Cleanup
$cleanupUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/cms/cleanup';
$deploy->addNotification(new Ddm\Deploy\Notification\Http($cleanupUrl, 'GET', false));

/*
 * send the notifications [required]
 */
$deploy->notify();