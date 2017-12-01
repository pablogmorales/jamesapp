<?php

namespace Ddm\Deploy\Notification;

use Ddm\Deploy\Message;

interface NotificationInterface {

	/**
	 * Send the deploy notification
	 */
	public function notify(Message $message);

}