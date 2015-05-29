<?php namespace App\Contracts;

interface SyncContract
{

	public function setSource($job_id, $data);
	public function setDestination($job_id, $data);
	public function fire($job_id);
    
}