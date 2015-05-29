<?php namespace App\Contracts\Implementations\Connector;

use App\Contracts\ConnectorContract;

class Elastic implements ConnectorContract
{

    protected $client = null;

    function __construct()
    {
        $this->client = app()->make('Es\Client');
    }

	public function buildConnectionString($syncdata) {

        $cnstr = $syncdata['driver'].'.';
        $cnstr .= $syncdata['storage_index'].'.';
        $cnstr .= $syncdata['storage_type'].'.';
        $cnstr .= $syncdata['fetch_field'];
        
        return $cnstr;

    }

    public function put($connectionString, $criteria, $sourceValues, $set) {

    	$connection = explode('.', $connectionString, 4);
        $index = trim($connection[1]);
        $type  = trim($connection[2]);
        $where = trim($connection[3]);

        if($where == '_id') {
            
            if(isset($set['fields'])) {
                 $setValues = array();
                 foreach($set['fields'] as $key) {
                    $setValues[$key] = $sourceValues[$key];
                 }
            }
            if(isset($set['except'])) {
                foreach($set['except'] as $key) {
                    unset($sourceValues[$key]);
                } 
                $setValues = $sourceValues;  
            }

            $body = ['doc'=>$setValues];

            return $this->client->update([
                'index' => $index,
                'type'  => $type,
                'id'    => $criteria,
                'body'  => $body
            ]);
            
        }

    }

    public function get($connectionString, $criteria, $source=null) {
    	
    }

}