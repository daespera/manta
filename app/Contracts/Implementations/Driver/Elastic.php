<?php namespace App\Contracts\Implementations\Driver;

use App\Contracts\DriverContract;

class Elastic extends DriverContract
{

	protected $client = null;

	function __construct()
	{
		$this->client = app()->make('Es\Client');
	}

	public function create()
	{

		if(empty($this->data_map['records'])) {
			$this->raiseError('No record definition found for Elastic driver');
			return;
		}

		$records = $this->data_map['records'];

		foreach($records as $data)
		{

			$index = $data['index'];
            $type = $data['type'];
			$body = $this->prepareData($data['fields'], $this->payload);

			try {

				# Index data to elastic
				$result = $this->client->index([
					'index' 	=> $index,
					'type' 		=> $type,
					'refresh' 	=> true,
					'body'		=> $body
				]);

				# Fire up synchronization here
                $this->sync($index.'.'.$type, $result);

				# Add result
				$this->addResult($index.'/'.$type, array_merge(['_id'=>$result['_id']], $body));

			}
			catch (Exception $e)
			{
				$this->raiseError('Elastic index failed');
				return;
			}
		}

	}

    public function update()
    {

        if(empty($this->operation_definition['identby'])) {
            $this->raiseError('No identifier definition found for Elastic driver');
            return;
        }

        $identifier = $this->operation_definition['identby'];
        foreach($identifier as $key=>$value) {
            $where = $key;
            $url_param = $value;
        }

        $criteria = \Request::get($url_param);

        if(empty($this->data_map['records'])) {
            $this->raiseError('No record definition found for Elastic driver');
            return;
        }

        $records = $this->data_map['records'];

        foreach($records as $data)
        {

            $body = $this->prepareData($data['fields'], $this->payload);

            $index = $data['index'];
            $type = $data['type'];
            
            try {

                # Retrieve elastic _id
                $params = [];
                $params['index'] = $index;
                $params['type'] = $type;
                $params['body']['query']['filtered']['query']['bool']['must']['match'][$where] = $criteria;
                $result = $this->client->search($params);

                if(!empty($result['hits']['hits'][0])) {

                    $_id = $result['hits']['hits'][0]['_id'];

                    $body = ['doc'=>$body];

                    # Update data in elastic
                    $update_result = $this->client->update([
                         'index' => $index,
                         'type'  => $type,
                         'refresh' => true,
                         'id'    => $_id,
                         'body'  => $body
                    ]);

                    # Fire up synchronization here
                    $this->sync($index.'.'.$type, $update_result);

                    # Add result
                    $result = $this->client->search($params);
                    $this->addResult($index.'/'.$type, $result['hits']['hits'][0]);

                }       

            }
            catch (Exception $e)
            {
                $this->raiseError('Elastic update failed');
                return;
            }

        }

    }

	public function flagIfSyncSource($storage, $obj)
    {

        if(!empty($this->operation_definition['sync'])) {

            $sync = $this->operation_definition['sync'];
            $cnstr = explode('.', $sync['source']['connection']);
            $db = explode('.', $storage);

            if(trim($cnstr[0]) == 'elastic' && trim($cnstr[1]) == trim($db[0]) && trim($cnstr[2]) == trim($db[1])) {
                            
                $sourceData = array(
                    'driver' => 'elastic',
                    'storage_index' => trim($cnstr[1]),
                    'storage_type'  => trim($cnstr[2]),
                    'fetch_field'  => trim($cnstr[3]),
                    'fetch_value'  => $obj[$cnstr[3]]
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
            $db = explode('.', $storage);

            if(trim($cnstr[0]) == 'elastic' && trim($cnstr[1]) == trim($db[0]) && trim($cnstr[2]) == trim($db[1])) {
                            
                $destinationData = array(
                    'driver' => 'elastic',
                    'storage_index' => trim($cnstr[1]),
                    'storage_type'  => trim($cnstr[2]),
                    'fetch_field'  => trim($cnstr[3]),
                    'fetch_value'  => $obj[$cnstr[3]],
                    'set' => $sync['set']
                );

                $yasha = app()->make('App\Contracts\YashaContract');
                $yasha->destination($destinationData);

            }            

        }
        
    }

}