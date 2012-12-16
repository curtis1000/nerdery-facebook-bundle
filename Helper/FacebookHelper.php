<?php

namespace Nerd\FacebookBundle\Helper;

class FacebookHelper 
{
    protected $_config;
    protected $_sdk;

    /**
     * __construct
     * Setup the SDK.
     * @param $config array
     * @return void
     */
    public function __construct($config)
    {
        $this->_config = $config;
        $this->initSdk();    
    }

    /**
     * initSdk
     * @return void
     */
    protected function initSdk()
    {
        // not all elements in $config apply to the sdk constructor
        $this->_sdk = new \Facebook(array(
            'appId' => $this->_config['appId'],
            'secret' => $this->_config['secret'],    
        ));
    }

    /**
     * __call
     * Pass through any method calls that the sdk can respond to.
     * @param $methodName string
     * @param $arguments array
     * @return mixed
     */
    public function __call($methodName, $arguments)
    {
        if (method_exists($this->_sdk, $methodName)) {
            return call_user_func_array(array($this->_sdk, $methodName), $arguments);
        }
    }

    /**
     * getLoginUrl
     * @return string
     * Overload this SDK method to include permission request parameter
     */
    public function getLoginUrl()
    {
        $loginUrl = $this->_sdk->getLoginUrl(array(
            'scope' => $this->_config['scope'],
        ));
        
        return $loginUrl;
    }


}
