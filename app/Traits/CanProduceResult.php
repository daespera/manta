<?php namespace App\Traits;

trait CanProduceResult
{

	protected $result = [];

	public function addResult($key, $data)
	{
		$this->result[$key][] = $data;
		return $this;
	}

	public function getResult()
	{
		return $this->result;
	}

}
