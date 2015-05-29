<?php namespace App\Contracts;

use App\Traits\CanProduceError;
use App\Traits\CanProduceResult;

abstract class DriverContract
{

	use CanProduceError, CanProduceResult;

	protected $operation_definition = [];
	protected $data_map = [];
	protected $payload = [];
	protected $config_reader = null;

	abstract function flagIfSyncSource($storage, $obj);
	abstract function flagIfSyncDestination($storage, $obj);

	public function setOperationDefinition($operation_definition)
	{
		$this->operation_definition = $operation_definition;
		return $this;
	}

	public function setDataMap($data_map)
	{
		$this->data_map = $data_map;
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

	public function write() 
	{
		if(is_callable([$this, $this->operation_definition['operation']])) {
			call_user_func([$this, $this->operation_definition['operation']]);
			return true;
		}
		return false;
	}

	protected function prepareData($keys, $payload)
	{
		return $this->config_reader->prepareData($keys, $payload);
	}

	protected function preparePivotData($data, $payload, $lastAffected)
	{
		return $this->config_reader->preparePivotData($data, $payload, $lastAffected);
	}

	protected function sync($storage, $obj) {
		$this->flagIfSyncSource($storage, $obj);
		$this->flagIfSyncDestination($storage, $obj);
	}

}