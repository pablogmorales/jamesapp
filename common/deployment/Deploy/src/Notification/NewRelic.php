<?php

namespace Ddm\Deploy\Notification;

use Ddm\Deploy\Message;

class NewRelic extends AbstractNotification {

	use CurlHttpTrait;

	protected $apiKey;

	protected $applicationName;

	protected $applicationId;

	protected $newRelicEndpoint = 'https://api.newrelic.com/deployments.xml';

	public function __construct($apiKey, $applicationName = null, $applicationId = null) {
		if (!isset($applicationName) && !isset($applicationId)) {
			$message = 'Either $applicationName or $applicationId must be passed to ' . __METHOD__;
			throw new \BadMethodCallException($message);
		}
		$this->apiKey = $apiKey;
		$this->applicationName = $applicationName;
		$this->applicationId = $applicationId;
	}

	public function notify(Message $message) {
		$requestHeaders = [
			'x-api-key' => $this->apiKey
		];
		$deployment = [];
		if (isset($this->applicationName)) {
			$deployment['app_name'] = $this->applicationName;
		} else {
			$deployment['app_ip'] = $this->applicationId;
		}
		if ($description = $message->getDescription()) {
			$deployment['description'] = $description;
		}
		if ($changelog = $message->getChangelog()) {
			$deployment['changelog'] = $changelog;
		}
		if ($revision = $message->getRevision()) {
			$deployment['revision'] = $revision;
		}
		if ($user = $message->getDeployer()) {
			$deployment['user'] = $user;
		}
		$requestData = compact('deployment');
		$this->setUrl($this->newRelicEndpoint);
		$this->setMethod('POST');
		$this->setHeaders($requestHeaders);
		$this->setData($requestData);
		$this->send();
	}
}