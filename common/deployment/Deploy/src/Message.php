<?php
namespace Ddm\Deploy;

class Message {

	protected $deployer;

	protected $repository;

	protected $revision;

	protected $description;

	protected $changelog;

	protected $target;

	protected $source;

	protected $timestamp;

	public function __construct($deployer, $repository, $revision, $description, $changelog, $target, $source, $timestamp) {
		$this->setDeployer($deployer);
		$this->setRepository($repository);
		$this->setRevision($revision);
		$this->setDescription($description);
		$this->setChangelog($changelog);
		$this->setTarget($target);
		$this->setSource($source);
		$this->setTimestamp($timestamp);
	}

	public function __toString() {
		return $this->toString();
	}

	public function toArray() {
		return [
			'deployer' => $this->deployer,
			'repository' => $this->repository,
			'revision' => $this->revision,
			'description' => $this->description,
			'changelog' => $this->changelog,
			'target' => $this->target,
			'source' => $this->source,
			'timestamp' => $this->timestamp
		];
	}

	public function toString() {
		return http_build_query((array) $this->toArray());
	}

	public function __call($name, array $arguments) {
		if (($set = strpos($name, 'set') === 0) || ($get = strpos($name, 'get') === 0)) {
			$property = strtolower(substr($name, 3));
			if (property_exists($this, $property)) {
				if ($set && array_key_exists(0, $arguments)) {
					$this->{$property} = $arguments[0];
				}
				return $this->{$property};
			}
		}
	}
}