<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\ConfigReaderContract as ConfigReader;
use App\Contracts\ProcessorContract as Processor;

class MantaController extends BaseController
{
    
    public function process(Request $request, ConfigReader $config_reader, Processor $processor, $storage, $operation)
    {

    	# Build processor object
    	$processor->setConfigReader($config_reader)
            ->setStorage($storage)
    		->setOperation($operation)
    		->setPayload(json_decode($request->get('payload', '[]'), true));
    	
    	# Fire up process
    	$processor->process();

    	# Return results
    	if($processor->hasError()) {
    		return $this->fail($processor->getError());
    	}
    	else {
    		return $this->success($processor->getResult());
    	}
    	
    }

}
