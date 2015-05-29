<?php namespace App\Contracts\Implementations\Sync;

use App\Contracts\SyncContract;

class Cache implements SyncContract
{

	public function setSource($job_id, $data) 
	{

		if(!\Cache::has($job_id)) {
			\Cache::put($job_id, ['source' => $data], 10);
		}
		else {
			$job = \Cache::get($job_id);
			\Cache::put($job_id, array_merge(['source' => $data], $job), 10);
		}

		$this->fire($job_id);

	}

	public function setDestination($job_id, $data) 
	{

		if(!\Cache::has($job_id)) {
			\Cache::put($job_id, ['destination' => $data], 10);
		}
		else {
			$job = \Cache::get($job_id);
			\Cache::put($job_id, array_merge(['destination' => $data], $job), 10);
		}

		$this->fire($job_id);

	}

	public function fire($job_id)
	{

		$job = \Cache::get($job_id);
	
		if(!empty($job['source']) && !empty($job['destination'])) {
     
            $source = $job['source'];
            $destination = $job['destination'];

            $sourceConnector = app()->make('App\Contracts\Implementations\Connector\\'.ucfirst($source['driver']));
            $sync_values = $sourceConnector->get($sourceConnector->buildConnectionString($source), $source['fetch_value']);

            $destinationConnector = app()->make('App\Contracts\Implementations\Connector\\'.ucfirst($destination['driver']));           
            $destinationConnector->put($destinationConnector->buildConnectionString($destination), $destination['fetch_value'], $sync_values, $destination['set']);

            \Cache::forget($job_id);

        }

	}


}