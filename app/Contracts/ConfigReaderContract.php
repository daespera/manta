<?php namespace App\Contracts;

interface ConfigReaderContract
{
 
    
    public function getOperationConfig($storage);
    public function getValidationConfig($storage);
    public function getDataMap($storage, $driver);
    public function buildValidationRules($operation, $config, $id=0);
    public function prepareData($keys, $payload);
    public function preparePivotData($data, $payload, $lastAffected);

}