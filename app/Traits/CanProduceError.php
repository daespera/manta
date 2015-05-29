<?php namespace App\Traits;

trait CanProduceError
{

	protected $has_error = false;
	protected $error = [];

	public function hasError()
	{
		return $this->has_error;
	}

	public function raiseError($message, $data = [])
	{
		$this->has_error = true;
		$this->error['message'] = $message;
		$this->error['data'] = $data;
		return $this;
	}

	public function getError()
	{
		if(empty($this->error['data'])) {
			return $this->error['message'];
		}
		return $this->error;
	}

	public function getErrorMessage()
	{
		return $this->error['message'];
	}

	public function getErrorData()
	{
		return $this->error['data'];
	}

}
