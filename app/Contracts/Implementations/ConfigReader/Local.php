<?php namespace App\Contracts\Implementations\ConfigReader;

use App\Contracts\ConfigReaderContract;

class Local implements ConfigReaderContract
{

	protected $storages_path = '';

	public function __construct()
	{
		$this->storages_path = base_path().'/app/Storages';
	}

	private function getConfigFile($storage, $type)
	{

		$config_file = $this->storages_path.'/'.$storage.'/config-'.$type.'.php';

		if(file_exists($config_file)) {
    		return require $config_file;
    	}

		return false;

	}
    
    public function getOperationConfig($storage) 
    {
    	return $this->getConfigFile($storage, 'operations');
    }

    public function getValidationConfig($storage)
    {
    	return $this->getConfigFile($storage, 'validation');
    }

    public function getDataMap($storage, $driver)
    {

    	$data_map_file = $this->storages_path.'/'.$storage.'/datamap-'.strtolower($driver).'.php';

		if(file_exists($data_map_file)) {
    		return require $data_map_file;
    	}

		return false;

    }

    public function buildValidationRules($operation, $config, $id=0)
    {

    	$rules = $config['common'];

        if(!empty($config[$operation])) {
            foreach($config[$operation] as $field => $rule) {
                if(!empty($rules[$field])) {
                    $rules[$field] = array_merge($rules[$field], $rule);    
                }
                else {
                    $rules[$field] = $rule;   
                }            
            }
        }        

        foreach($rules as &$rule) {

            if($id!=0) {
                foreach($rule as &$ruledef) {
                    $exp = explode(':', $ruledef, 2);
                    if(!empty($exp)) {
                        if(strtolower(trim($exp[0])) == 'unique') {
                            $ruledef .= ','.$id;        
                        }
                    }                    
                }    
            }        

            $rule = implode('|', $rule);
        }        
        
        return $rules;

    }

    public function preparePivotData($data, $payload, $lastAffected)
    {
        
        foreach($data['pivot']['iterator'] as $payload_iterator => $iterator_field) {}
        foreach($data['pivot']['inherit'] as $inherit_key => $inherit_field) {}

        $sets = [];

        if(!empty($payload[$payload_iterator])) {

            foreach($payload[$payload_iterator] as $value) {

                $fields = [];
                $fields[$iterator_field] = $value;
                $fields[$inherit_field] = $lastAffected->$inherit_key;

                if(!empty($data['fields'])) {
                    $otherFields = $this->prepareData($data['fields'], $payload); 
                    $sets[] = array_merge($fields, $otherFields);   
                }
                else {
                    $sets[] = $fields;
                }
                

            }

        }
    
        return $sets;  

    }

    public function prepareData($keys, $payload) 
    {

        $body = [];

        foreach($keys as $payloadKey => $dataField) {

            $arraySource = false;
            if(strpos($payloadKey, '[]')) {
                $arraySource = true;
                $payloadKey = trim(str_replace('[]', '', $payloadKey));
            }            

            if(isset($payload[$payloadKey])) {

                if(is_array($payload[$payloadKey])) {
                    $arrayField = [];
                    foreach($payload[$payloadKey] as $value) {
                        $arrayField = array_merge_recursive($arrayField, $this->parseDataField($value, $dataField, $payload));
                    }
                    reset($arrayField);
                    $first_key = key($arrayField);
                    if(isset($arrayField[$first_key])) {
                        if(count($arrayField[$first_key]) == 1) {
                            $temp = [];
                            $temp[] = $arrayField[$first_key];
                            $arrayField[$first_key] = $temp;
                        }
                    }
                    $body = array_merge($body, $arrayField);
                }
                else {
                    $body = array_merge($body, $this->parseDataField($payload[$payloadKey], $dataField, $payload));    
                }
                
            }                

        }   

        return $body;

    }

    private function parseDataField($startValue, $dataField, $payload) {

        $body = [];

        if(is_array($dataField)) {            

            if(!empty($dataField['aggregate'])) {            

                $cnstr = explode('.', $dataField['aggregate']['connection']);
                $driver = trim($cnstr[0]);

                $connector = app()->make('App\Contracts\Implementations\Connector\\'.ucfirst(strtolower($driver)));             
                $value = $connector->get($dataField['aggregate']['connection'], $startValue, $dataField['aggregate']['source']);   

            }
            else {
                $value = $startValue;
            }

            if(!empty($dataField['pre'])) {

                $preprocessor = app()->make('App\Contracts\PreprocessorContract');

                $exp = explode(':', $dataField['pre'], 2);
                
                if(count($exp) == 1) {
                    $value = call_user_func([$preprocessor, $dataField['pre']], $value);    
                }
                else {
                    $method = trim($exp[0]);
                    $keys = explode(',',$exp[1]);
                    $params = [];
                    foreach($keys as $payloadKey) {
                        $params[] = trim($payload[$payloadKey]);
                    }
                    $value = call_user_func_array([$preprocessor, $method], $params);
                }

            }

            $body[$dataField['field']] = $value;
            
        }
        else {
            $body[$dataField] = $startValue;
        }

        return $body;

    }

}