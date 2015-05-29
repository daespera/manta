<?php namespace App\Contracts;

interface ConnectorContract
{

	public function buildConnectionString($syncdata);
	public function put($connectionString, $criteria, $sourceValues, $set);
	public function get($connectionString, $criteria, $source=null);
   
}