<?php

namespace Ddm\Deploy;

use Ddm\Deploy\Service\ServiceInterface;
use Ddm\Deploy\Notification\NotificationInterface;

/**
 * A deploy receiver & notification utility
 *
 */
class DeployHandler {

	/**
	 * ServiceInterface
	 *
	 * @var ServiceInterface
	 */
	protected $service;

	/**
	 * NotificationInterface
	 *
	 * @var array
	 */
	protected $notifications;

	/**
	 *
	 * @param ServiceInterface $service
	 * @param array NotificationInterface $notifications
	 * @param string $validate
	 */
	public function __construct(ServiceInterface $service = null, $notifications = []) {
		if (isset($service)) {
			$this->setService($service);
		}
		if (!empty($notifications)) {
			array_map([$this, 'addNotification'], (array) $notifications);
		}
	}

	/**
	 * Validate the notification from the service, and trigger notifications
	 */
	public function notify() {
		if (empty($this->service)) {
			$this->stop(200, 'No services');
		}
		if (!$this->service->validate()) {
			$this->stop(400);
		}
		$message = $this->service->message();
		foreach ($this->notifications as $notification) {
			$notification->notify($message);
		}
		$this->stop(200);
	}

	/**
	 *
	 * @param ServiceInterface $service
	 */
	public function setService(ServiceInterface $service) {
		$this->service = $service;
	}

	/**
	 *
	 * @param NotificationInterface $notification
	 */
	public function addNotification(NotificationInterface $notification) {
		$this->notifications[] = $notification;
	}

	/**
	 *
	 * @param integer $code
	 * @param string $response
	 */
	protected function stop($code = 200, $response = '') {
		http_response_code($code);
		echo $response;
		exit();
	}
}