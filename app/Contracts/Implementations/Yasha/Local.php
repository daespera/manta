<?php namespace App\Contracts\Implementations\Yasha;

use App\Contracts\YashaContract;
use Webpatser\Uuid\Uuid as Uuid;
use App\Traits\CanCurl;

class Local implements YashaContract
{

	use CanCurl;
	
	private $sync_id = '';
	private $yasha_url = '';

	function __construct() 
	{
		$this->sync_id = (string) Uuid::generate(4);
		$this->yasha_url = url('yasha');
	}

	public function source($data) 
	{

		$this->post($this->yasha_url.'/source', [
			'sync_id' => $this->sync_id,
			'data' => $data
		]);

	}

	public function destination($data)
	{

		$this->post($this->yasha_url.'/destination', [
			'sync_id' => $this->sync_id,
			'data' => $data
		]);

	}


}