<?php

namespace Ddm\Deploy\Service;

use Ddm\Deploy\Message;

/**
 * Beanstalk deploy notification service receiver
 *
 * @link http://support.beanstalkapp.com/customer/portal/articles/75806
 */
class Beanstalk extends AbstractService {

	/**
	 * (non-PHPdoc)
	 * @see \Ddm\Deploy\Service\ServiceInterface::validate()
	 *
	 * {"author":"author username", "repository":"beanstalk", "author_name":"John Smith", "comment":"example", "author_email":"johnsmith@example.com", "server":"server example", "environment":"development", "revision":"5","deployed_at":"deployed at date","repository_url":"git@example.beanstalkapp.com:/example.git","source":"beanstalkapp"}
	 *
	 */
	public function validate() {
		if ($this->requestMethodIs('POST')) {
			$data = $this->getData();
			if ($data && isset($data['source']) && $data['source'] == 'beanstalkapp') {
				$this->data = $data;
				return true;
			}
		}
		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Ddm\Deploy\Service\ServiceInterface::notification()
	 */
	public function message() {
		$deployer = "{$this->data['author_name']} [{$this->data['author_email']}]";
		$repository = $this->data['repository'];
		$revision = $this->data['revision'];
		$description = $this->data['comment'];
		$target = "{$this->data['server']} ({$this->data['environment']})";
		$source = $this->data['source'];
		$timestamp = $this->data['deployed_at'];
		return new Message($deployer, $repository, $revision, $description, '', $target, $source, $timestamp);
	}

	/**
	 * Get request data from beanstalk
	 */
	protected function getData() {
		if ($body = @file_get_contents('php://input')) {
			return @json_decode($body, true);
		}
	}
}