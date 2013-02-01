<?php

namespace Nerd\FacebookBundle\Helper;

use Symfony\Component\Config\Definition\Exception\Exception;

class FacebookHelper
{
    protected $_config;
    protected $_sdk;

    /**
     * __construct
     * Setup the SDK.
     * @param $config array
     * @return \Nerd\FacebookBundle\Helper\FacebookHelper
     */
    public function __construct($config, $serviceContainer)
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
            'cookie' => true,
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
     * @param $redirectUri
     * @return string
     * Overload this SDK method to include permission request parameter
     */
    public function getLoginUrlWithRedirectUri($redirectUri)
    {
        $loginUrl = $this->_sdk->getLoginUrl(array(
            'scope' => $this->_config['scope'],
            'redirect_uri' => $redirectUri,
        ));
        
        return $loginUrl;
    }

    public function userHasAuthorized()
    {
        $id = $this->_sdk->getUser();
        return (bool) $id;
    }

    public function getPageTabDeepLinkUrl($name, $pattern, $defaults)
    {
        if (empty($this->_config['pageTabUrl'])) {
            throw new Exception('$this->_config->pageTabUrl is empty.');
        }

        $url = $this->_config['pageTabUrl'] . '&app_data=' . urlencode(json_encode(
            array(
                'deepLink' => array(
                    'name'      => $name,
                    'pattern'      => $pattern,
                    'defaults'    => $defaults,
                )
            )
        ));

        return $url;
    }
}
