<?php

namespace Daytalytics;


class Mailer {

	public static function mail_admins($subject, $message) {
		static::send(getenv('APP_ADMIN_EMAIL'), $subject, $message);
	}

	public static function mail_monoriting_task($subject, $message) {
		static::send(getenv('APP_MONITORING_EMAIL'), $subject, $message);
	}

	public static function send($recipients, $subject, $message) {
		if ($recipients = implode(', ', (array) $recipients)) {
			mail($recipients, $subject, $message);
		}
	}
}

?>