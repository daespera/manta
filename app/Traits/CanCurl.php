<?php namespace App\Traits;

trait CanCurl
{

    protected $_curl = null;

    public function get($url) 
    {
    
        $this->_curl = curl_init();
        $this->setOption(CURLOPT_URL, $url);

        return $this->_exec();

    }

    public function post($url, $params = array()) 
    {

        $this->_curl = curl_init();
        $this->setOption(CURLOPT_URL, $url);        
        $this->setOption(CURLOPT_POST, 1);
        $this->setOption(CURLOPT_POSTFIELDS, http_build_query($params));

        return $this->_exec();

    }

    protected function setOption($option, $value) 
    {

        curl_setopt($this->_curl, $option, $value);

    }

    protected function _exec() 
    {

        $this->setOption(CURLOPT_RETURNTRANSFER, true);     
        $response = curl_exec($this->_curl);
        curl_close($this->_curl);

        return json_decode($response, true);

    }

}