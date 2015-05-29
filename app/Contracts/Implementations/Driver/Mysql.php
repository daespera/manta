<?php namespace App\Contracts\Implementations\Driver;

use App\Contracts\DriverContract;

class Mysql extends DriverContract
{

	protected $lastAffected = null;

	public function create()
	{

		if(empty($this->data_map['records'])) {
			$this->raiseError('No record definition found for Mysql driver');
			return;
		}

		$records = $this->data_map['records'];

		foreach($records as $data)
		{

			$table = $data['table'];
			
			# Write into a flat table
			if(!isset($data['pivot'])) {

				$set = $this->prepareData($data['fields'], $this->payload);

				try {

					# Insert data into Mysql
					$lastInsertId = \DB::table($table)->insertGetId($set);
					$obj = \DB::table($table)->where('id', $lastInsertId)->first();

	                $this->lastAffected = $obj;

	                # Fire up synchronization here
	                $this->sync($table, $obj);

	                # Add result
	                $this->addResult($table, array_merge(['id'=>$lastInsertId], $set));

				}
				catch (Exception $e)
				{
					$this->raiseError('Mysql insert failed');
					return;
				}
				
				
			}
			# Write into a pivot table
			else {

				$sets = $this->preparePivotData($data, $this->payload, $this->lastAffected); 

				foreach($sets as $set) {

					try {

	                    # Insert data into Mysql
	                    $lastInsertId = \DB::table($table)->insertGetId($set);

	                    # Add result
	                    $this->addResult($table, array_merge(['id'=>$lastInsertId], $set));
	                    
                    }
					catch (Exception $e)
					{
						$this->raiseError('Mysql insert failed');
						return;
					}

                }

			}

		}

	}

	public function update()
	{

		if(empty($this->operation_definition['identby'])) {
			$this->raiseError('No identifier definition found for Mysql driver');
			return;
		}

		$identifier = $this->operation_definition['identby'];
        foreach($identifier as $key=>$value) {
            $where = $key;
            $url_param = $value;
        }

        $criteria = \Request::get($url_param);

        if(empty($this->data_map['records'])) {
			$this->raiseError('No record definition found for Mysql driver');
			return;
		}

		$records = $this->data_map['records'];

		foreach($records as $data)
		{

			$table = $data['table'];
			
			# Write into a flat table
			if(!isset($data['pivot'])) {

				$set = $this->prepareData($data['fields'], $this->payload);

				try {

					# Update data into Mysql
					foreach($set as $key=>$value) {
                    	\DB::table($table)->where($where, $criteria)->update($set);
	                }

	                $obj = \DB::table($table)->where($where, $criteria)->first();
	                $this->lastAffected = $obj;

	                # Fire up synchronization here
	                $this->sync($table, $obj);

	                # Add result
	                $this->addResult($table, json_decode(json_encode($obj)));

				}
				catch (Exception $e)
				{
					$this->raiseError('Mysql update failed');
					return;
				}
				
				
			}
			# Write into a pivot table
			else {

				$sets = $this->preparePivotData($data, $this->payload, $this->lastAffected); 

				if(count($sets)) {
					
					$pKeys = $data['pivot']['key'];
                    $where = [];

                    foreach($pKeys as $key) {
                        $where[$key] = $sets[0][$key];
                    }

                    try {

	                    # Delete old mapping
	                    \DB::table($table)->where($where)->delete();

	                    foreach($sets as $set) {
	                        
	                        # Insert data into Mysql
	                        $lastInsertId = \DB::table($table)->insertGetId($set);
	                    	
	                    	# Add result
	                    	$this->addResult($table, array_merge(['id'=>$lastInsertId], $set));
	                    	
	                    }
	                    
                    }
					catch (Exception $e)
					{
						$this->raiseError('Mysql delete/insert failed');
						return;
					}

				}

			}

		}

	}

	public function flagIfSyncSource($storage, $obj)
    {

        if(!empty($this->operation_definition['sync'])) {

            $sync = $this->operation_definition['sync'];
            $cnstr = explode('.', $sync['source']['connection']);

            if(trim($cnstr[0]) == 'mysql' && trim($cnstr[1]) == trim($storage)) {
                            
                $sourceData = array(
                    'driver' => 'mysql',
                    'storage_table' => trim($storage),
                    'fetch_field'  => trim($cnstr[2]),
                    'fetch_value'  => $obj->$cnstr[2]
                );

                $yasha = app()->make('App\Contracts\YashaContract');
                $yasha->source($sourceData);

            }       

        }

    }

    public function flagIfSyncDestination($storage, $obj)
    {

        if(!empty($this->operation_definition['sync'])) {

            $sync = $this->operation_definition['sync'];
			$cnstr = explode('.', $sync['destination']['connection']);

            if(trim($cnstr[0]) == 'mysql' && trim($cnstr[1]) == trim($storage)) {
                            
                $destinationData = array(
                    'driver' => 'mysql',
                    'storage_table' => trim($storage),
                    'fetch_field'  => trim($cnstr[2]),
                    'fetch_value'  => $obj->$cnstr[2],
                    'set' => $sync['set']
                );

                $yasha = app()->make('App\Contracts\YashaContract');
                $yasha->destination($destinationData);

            }          

        }
        
    }

}