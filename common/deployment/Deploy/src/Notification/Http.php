<?php

namespace Ddm\Deploy\Notification;

use Ddm\Deploy\Message;

class Http extends AbstractNotification {

	use CurlHttpTrait;

	public function __construct($url, $method = 'POST', $data = [], $headers = []) {
		$this->setUrl($url);
		$this->setMethod($method);
		$this->setHeaders($headers);
		$this->setData($data);
	}

	public function notify(Message $message) {
		if ($this->data !== false) {
			$this->data += $message->toArray();
		}
		$this->send();
	}
}