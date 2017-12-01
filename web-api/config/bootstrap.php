<?php
//Autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

//Env config
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
}
if (!getenv('DDM_COMMON')) {
    putenv('DDM_COMMON=/var/www/common/');
}
//New Relic
if (extension_loaded('newrelic') && ($appName = getenv('NEW_RELIC_APP_NAME'))) {
    $iniName = ini_get('newrelic.appname');
    if (!$iniName || $iniName != $appName) {
        newrelic_set_appname($appName);
    }
    if (php_sapi_name() == "cli") {
        $txnName = $_SERVER['SCRIPT_NAME'];
    } else {
        $txnName = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    if (!empty($txnName)) {
        newrelic_name_transaction(strtolower($txnName));   
    }  
}

//Config
require_once __DIR__. '/config.php';

//Timezone
date_default_timezone_set('UTC');

//Env switches
switch(getenv('APP_ENVIRONMENTS')) {
    case 'development';
    case false:
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    break;
    case 'staging';
        error_reporting(E_ALL ^ E_STRICT);
        ini_set('display_errors', 1);
    break;
    case 'production';
        error_reporting(E_ALL ^ E_STRICT);
        ini_set('display_errors', 0);
    break;
}

//Error handler
require_once getenv('DDM_COMMON') . 'ErrorHandler/error.php';