<?php
$allowed_ips = array(
    '127.0.0.1',        // Localhost

	//Rackspace Web1 PublicNet (php upgrade server)
	'23.253.233.5',         //Primary / doubledotmedia.com
	'23.253.233.12',        //zeadoo.com
	'23.253.233.15',        //traffictravis.com
	'23.253.233.17',        //salehoo.com
	'23.253.232.16',        //affilorama.com
	//Rackspace Web1 ServiceNet (php upgrade server)
	'10.223.224.8',

	//Rackspace Web1 PublicNet
	'23.253.97.164', 	//Primary / doubledotmedia.com
	'23.253.97.93', 	//zeadoo.com
	'23.253.98.140', 	//traffictravis.com
	'23.253.98.133', 	//salehoo.com
	'104.130.133.4', 	//affilorama.com
	//Rackspace Web1 ServiceNet
	'10.223.209.169',

	//Linode - DDM/CIT
	'45.33.27.71',
	//Linode - Traffic Travis
	'45.33.28.100',
	//Linode - SaleHoo
	'45.33.125.26',
	//Linode - App1
	'173.255.193.185',
    //Linode - Affilorama
    '45.79.7.177',

	'202.124.96.118',   // DDM Office
	'103.233.22.111',   // Paul (Home/Primary)
    '103.237.42.20',   // Paul (Office)
	'203.86.205.252',   // Simon

	//Beanstalk IPs (http://support.beanstalkapp.com/customer/portal/articles/75796-ip-addresses-for-access-to-beanstalk)
	'50.31.156.48',
	'50.31.156.49',
	'50.31.156.50',
	'50.31.156.51',
	'50.31.156.52',
	'50.31.156.53',
	'50.31.156.54',
	'50.31.156.55',
	'50.31.156.56',
	'50.31.156.57',
	'50.31.156.58',
	'50.31.156.59',
	'50.31.156.60',
	'50.31.156.61',
	'50.31.156.62',
	'50.31.156.63',
	'50.31.156.64',
	'50.31.156.65',
	'50.31.156.66',
	'50.31.156.67',
	'50.31.156.68',
	'50.31.156.69',
	'50.31.156.70',
	'50.31.156.71',
	'50.31.156.72',
	'50.31.156.73',
	'50.31.156.74',
	'50.31.156.75',

	'50.31.189.108',
	'50.31.189.109',
	'50.31.189.110',
	'50.31.189.111',
	'50.31.189.112',
	'50.31.189.113',
	'50.31.189.114',
	'50.31.189.115',
	'50.31.189.116',
	'50.31.189.117',
	'50.31.189.118',
	'50.31.189.119',
	'50.31.189.120',
	'50.31.189.121',
	'50.31.189.122'
);

if (preg_match('/^10\./', $_SERVER['REMOTE_ADDR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$requested_by_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$requested_by_ip = $_SERVER['REMOTE_ADDR'];
}
