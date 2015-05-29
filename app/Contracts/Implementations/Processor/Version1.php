<?php namespace App\Contracts\Implementations\Processor;

use App\Contracts\ProcessorContract;

class Version1 extends ProcessorContract
{

	protected $yasha = null;

	public function process()
	{

		# Load operation config file
		$operation_config = $this->config_reader->getOperationConfig($this->storage);
		if(empty($operation_config)) {
			$this->raiseError('Failed reading operation configuration', [
				'storage' => $this->storage
			]);
			return;
		}

		# Determine operation definition
		if(empty($operation_config[$this->operation])) {
			$this->raiseError('Requested operation not defined for this storage', [
				'storage' 	=> $this->storage,
				'operation' => $this->operation
			]);
			return;
		}
		$operation_definition = $operation_config[$this->operation];

		# Load validation config file
		$validation_config = $this->config_reader->getValidationConfig($this->storage);
		if(empty($validation_config)) {
			$this->raiseError('Failed reading validation configuration', [
				'storage' => $this->storage
			]);
			return;
		}

		# Build validation rules
		$validation_rules = $this->config_reader->buildValidationRules(
			$operation_definition['operation'],
			$validation_config,
			\Request::input('id', 0)
		);

		# Validate payload
		$validator = \Validator::make($this->payload, $validation_rules);
		if($validator->fails()) {
          	$this->raiseError('Payload validation failed', $validator->messages());
            return;
        }

        # Write through each configured driver
        if(empty($operation_definition['drivers'])) {
        	$this->raiseError('No drivers are configured for this operation', [
        		'operation' => $operation_definition['operation']
        	]);
			return;
        }
        $drivers = $operation_definition['drivers'];

        foreach($drivers as $driver_name) {

        	# Load driver
        	$driver = app()->make('App\Contracts\Implementations\Driver\\'.ucfirst(strtolower($driver_name)));

        	# Get data map
        	$data_map = $this->config_reader->getDataMap($this->storage, $driver_name);
        	if(empty($data_map)) {
				$this->raiseError('Failed reading data map for driver', [
        			'storage' 	=> $this->storage,
        			'driver'	=> $driver_name
        		]);
				return;
			}

        	# Build driver object
        	$driver->setConfigReader($this->config_reader)
        		->setOperationDefinition($operation_definition)
        		->setDataMap($data_map)
        		->setPayload($this->payload);
        	
        	# Write through driver
        	if(!$driver->write()) {
        		$this->raiseError('Operation for driver not defined', [
        			'driver' 	=> $driver_name,
        			'operation'	=> $operation_definition['operation']
        		]);
				return;
        	}

        	# Return results
	    	if($driver->hasError()) {
	    		$this->raiseError($driver->getErrorMessage(), $driver->getErrorData());
        		return;
	    	}

	    	$this->addResult($driver_name, $driver->getResult());

        }

        return true;

	}

}