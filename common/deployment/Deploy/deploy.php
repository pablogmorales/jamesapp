<?php

require __DIR__ . '/vendor/autoload.php';

//ensure IP restricted
$common = dirname(dirname(__DIR__));
require $common . '/trusted_ips.php';

$isPrivate = !filter_var($requested_by_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE);
if (!$isPrivate) {
	if (!in_array($requested_by_ip, $allowed_ips)) {
		http_response_code(401);
		exit();
	}
}