<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as LumenBaseController;

class BaseController extends LumenBaseController
{

	private $headers = [
        'Content-type'=> 'application/json; charset=utf-8',
        'Access-Control-Allow-Origin'=>'*',
        'Access-Control-Allow-Methods'=> 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'=>'Origin, Content-Type, X-Auth-Token'
    ];
    
	protected function success($data = []) 
    {
        
        if(!empty($data)) {
            return response()->json([
                'timestamp' => date('Y-m-d H:i:s'),
                'http_code' => '200',
                'status' => 'success',              
                'data' => empty($data) ? '' : $data
            ], 200, $this->headers, JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
                'timestamp' => date('Y-m-d H:i:s'),
                'http_code' => '200',
                'status' => 'success',              
            ], 200, $this->headers, JSON_UNESCAPED_UNICODE);
        }           
        
    }
    protected function fail($data) 
    {

        if(is_array($data)) {
            return response()->json([
                'timestamp' => date('Y-m-d H:i:s'),
                'http_code' => '400',
                'status' => 'fail',             
                'data' => $data
            ], 400, $this->headers, JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
            	'timestamp' => date('Y-m-d H:i:s'),
                'http_code' => '400',
                'status' => 'fail',             
                'message' => $data
            ], 400, $this->headers, JSON_UNESCAPED_UNICODE);
        }
        
    }

}