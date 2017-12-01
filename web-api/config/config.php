<?php
global $config;

$allowed_ips = [];
if (!isset($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}
require getenv('DDM_COMMON') . 'trusted_ips.php';

$config = [];

//IP access restrictions
$config['ip_auth'] = [];
//The scripts in 'scripts_to_check' will only be accessable from 'allowed_ips'.
//This restriction only applies in production mode.
//The elements in 'scripts_to_check' will be checked against basename($_SERVER['PHP_SELF'])
$config['ip_auth']['scripts_to_check'] = ['cron.php'];
$config['ip_auth']['allowed_ips'] = $allowed_ips;

//These settings limit the number of requests that can come from an IP
$config['rate_limiting'] = [];
$config['rate_limiting']['default_limit'] = 10000;
$config['rate_limiting']['module_limits'] = [
//     'ModuleName' => integer usage limit
    'MajesticSeo' => 3500
];

$config['rate_limiting']['explicit_limits'] = [
//     'auth token' => [
//         'ModuleName' => integer usage limit
//     ]
];