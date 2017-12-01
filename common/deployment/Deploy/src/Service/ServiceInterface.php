<?php

namespace Ddm\Deploy\Service;

interface ServiceInterface {

	/**
	 * Validate the incoming request/data
	 */
	public function validate();

	/**
	 * Return the validated request data from the deploy service
	 *
	 * @return \Ddm\Deploy\Message
	 */
	public function message();

}