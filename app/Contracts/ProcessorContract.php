<?php namespace App\Contracts;

use App\Traits\CanProduceError;
use App\Traits\CanProduceResult;

abstract class ProcessorContract
{

	use CanProduceError, CanProduceResult;

	protected $storage = '';
	protected $operation = '';
	protected $payload = [];

	protected $config_reader = null;

	abstract public function process();

	public function setStorage($storage)
	{
		$this->storage = $storage;
		return $this;
	}

	public function setOperation($operation)
	{
		$this->operation = $operation;
		return $this;
	}

	public function setPayload($payload)
	{
		$this->payload = $payload;
		return $this;
	}

	public function setConfigReader($config_reader)
	{
		$this->config_reader = $config_reader;
		return $this;
	}

}