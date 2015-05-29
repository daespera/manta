<?php namespace App\Contracts\Implementations\PreProcessor;

use App\Contracts\PreProcessorContract;

class Core extends PreProcessorContract
{

	public function castToFloat($str)
    {
        return floatval($str);
    }

	public function castToInt($str)
    {
        return (int) $str;
    }

	public function concat()
	{
		return implode(' ', func_get_args());	
	}

	public function toLower($str) 
	{
		return strtolower($str);
	}

	public function toArray($str)
    {
        return json_decode($str, true);
    }

    public function toMysqlTime($str)
    {
        if(empty($str)) { return null; }
        return date('H:i:s', $str);
    }

    public function toMysqlDateTime($str)
    {
        if(empty($str)) { return null; }
        return date('Y-m-d H:i:s', $str);
    }

    public function toSlug($str)
    {
    	return str_slug($str, '-');
    }

	public function toUpper($str) 
	{
		return strtoupper($str);
	}

}