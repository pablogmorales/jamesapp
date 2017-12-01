<?php

namespace Ddm\Deploy\Service;

abstract class AbstractService implements ServiceInterface {

	protected $data = [];

	public function requestMethodIs($method) {
		return $this->getRequestMethod() === strtoupper($method);
	}

	protected function getRequestMethod() {
		return $_SERVER['REQUEST_METHOD'];
	}
}