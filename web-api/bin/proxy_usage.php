<?php
#$log_file = '/var/log/apache/access_log';
$log_file = '/tmp/last_hour';

$ProxyRequest = new ProxyRequest;

$log_contents = file($log_file);
$start = $end = '';
$count = $data = 0;

$match_modules = array(
	'Amazon API' => 'awis.amazonaws.com/?Action=UrlInfo',
	'AERS' => 'api.researchadvanced.com',
	'eBay' => 'svcs.ebay.com',
	'eBayShoppingApi' => 'open.api.ebay.com/shopping',
	'Google' => 'ttp://www.google.', 
	'GoogleNews' => 'news.google.com',
	'GooglePR' => 'toolbarqueries.google.com',
	'Majestic' => 'lightapi.majesticseo.com/api_command.php', 
	'SeoMoz Link' => 'ttp://lsapi.seomoz.com',
	'WantItNow' => '/wantitnow',
	'Yahoo' => 'search.yahooapis.com',
	);

$data_usage = array();
foreach($match_modules as $key=>$val) {
	$data_usage[$key]['data'] = 0;
	$data_usage[$key]['counts'] = 0;
}


foreach ($log_contents as $line) {
	If (stripos($line, 'api.daytalytics.')) {
		continue;
	}
	if (stripos($line, 'server-status')) {
		continue;
	}
	if (stripos($line, 'w00tw00t')) {
		continue;
	}
	if (stripos($line, 'favicon')) {
		continue;
	}

	$ProxyRequest->parseLine($line);
	if (empty($start)) {
		$start = $ProxyRequest->request_date . " " . $ProxyRequest->request_time;
	}
	$data = $data + $ProxyRequest->response_size;
	$count++;

	$matched = false;
	foreach ($match_modules as $key=>$val) {
		if (stripos($ProxyRequest->request_url, $val) > 0) {
			$data_usage[$key]['counts']++;
			$data_usage[$key]['data'] = $data_usage[$key]['data'] + $ProxyRequest->response_size;
			$matched = true;
			continue;
		}
	}
	if ($matched == false) {
		echo "{$ProxyRequest->request_url}\n";
	}


#	if ($ProxyRequest->response_size > 30000){ 
	#if (stripos($ProxyRequest->request_url, '20mascara')) {
	#	echo "{$ProxyRequest->request_ip} large response {$ProxyRequest->response_size} for url {$ProxyRequest->request_url}\n";	
	#}
	#echo "Request {$ProxyRequest->status_code} length {$ProxyRequest->response_size} url {$ProxyRequest->request_url}\n";
	#echo $ProxyRequest->request_url;
}
$end = $ProxyRequest->request_date . " " . $ProxyRequest->request_time;

echo "Between {$start} and {$end}\n";
echo "{$count} requests using " . formatBytes($data) . "\n\n";
foreach ($match_modules as $key=>$val) {
	echo "{$key} requests {$data_usage[$key]['counts']} with data " . formatBytes($data_usage[$key]['data'], 2) . "\n";
}


class ProxyRequest {
	var $request_ip;
	var $request_date;
	var $request_time;
	var $raw_url;
	var $request_url;
	var $status_code;
	var $response_size;
	var $user_agent;
	var $method;

	function parseLine($line) {
		$reg_ex = "^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})"; //ip
		$reg_ex.= " .*? \[(.*?) \+0000\]"; //date
		$reg_ex.= " \"(GET|POST) (.*?) HTTP"; //metho and urd
		#$reg_ex.= " \"GET (.*?) HTTP"; //url
		$reg_ex.= ".*?\" ([0-9]{3})"; //status code 
		$reg_ex.= " ([0-9]{1,})"; //response_size 
		$reg_ex.= " \"-\" \"(.*?)\"$"; //use agent
		if (!preg_match("/$reg_ex/",$line, $matches)) {
			echo "Line failed to match: $line\n";
			return false;
		}

		$this->request_ip = $matches[1];
		$this->request_date = $this->decodeDate($matches[2]);
		$this->request_time = $this->decodeTime($matches[2]);
		$this->method = $matches[3];
		$this->raw_url = $matches[4];
		$this->request_url = $this->decodeUrl($matches[4]);
		$this->status_code = $matches[5];
		$this->response_size = $matches[6];
		$this->user_agent = $matches[7];
	}

	function decodeDate($apache_date_time) {
		return array_shift(explode(':', $apache_date_time));
	}

	function decodeTime($apache_date_time) {
		$parts = explode(':', $apache_date_time);
		return "{$parts[1]}:{$parts[2]}:{$parts[3]}";
	}

	function decodeUrl($url) {
		if (preg_match('/&url=(.*?)&base/', $url, $matches)) {
			return base64_decode(urldecode($matches[1]));
		}
		return $url;
	}

}

function formatBytes($bytes, $precision = 2) { 
    $symbols = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
    $exp = floor(log($bytes)/log(1024));

    return sprintf('%.2f '.$symbols[$exp], ($bytes/pow(1024, floor($exp))));
}
