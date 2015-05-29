<?php namespace App\Contracts\Implementations\Connector;

use App\Contracts\ConnectorContract;

class Mysql implements ConnectorContract
{

	public function buildConnectionString($syncdata) {

        $cnstr = $syncdata['driver'].'.';
        $cnstr .= $syncdata['storage_table'].'.';
        $cnstr .= $syncdata['fetch_field'];
        
        return $cnstr;

    }

    public function get($connectionString, $criteria, $source=null) {

        $connection = explode('.', $connectionString, 3);
        $table = trim($connection[1]);
        $where   = trim($connection[2]);    

        $obj = \DB::table($table)->where($where, $criteria)->first();
        $data = json_decode(json_encode($obj), true);

        if(empty($source)) {            
            return $data;
        }
        else {
            return $data[$source];    
        }
        
    }

    public function put($connectionString, $criteria, $sourceValues, $set) {
    	
    }
	
}