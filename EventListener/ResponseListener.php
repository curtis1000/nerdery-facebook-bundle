<?php

namespace Nerd\FacebookBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * This listener's job is to redirect to the appropriate 
 * page if this is a deep link. Deep links are achieved 
 * by utilizing facebook's app_data parameter in the format:
 * <pageTabUrl>?...&app_data={"deepLink":{"route":"_tab_canvas", 
 * params: [something: "something"]}}
 *
 * The above example omits url encoding for illustration purposes
 *
 * The params property can also be a json object instead of an array
 * if you need it to be passed as an associative array.
 *
 * Facebook passes app_data into our app via the signed request.
 */

class ResponseListener
{
    protected $_facebookHelper;

    public function __construct($facebookHelper) 
    {
        $this->_facebookHelper = $facebookHelper;
    }
    
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            
            // get the signed request.
            $signedRequest = $this->_facebookHelper->getSignedRequest(); 

            // check for the prescence of app_data in the signed request
            if (! empty($signedRequest['app_data'])) {
                
                $appData = json_decode($signedRequest['app_data']);
                
                // proceed if app_data is a json object with deepLink property
                if (is_object($appData) && ! empty($appData->deepLink)) {
                    
                    // does deepLink have a route property?
                    if (! empty($appData->deepLink->route)) {

                        $route = $appData->deepLink->route;

                        // set the params if they exist
                        if (! empty($appData->deepLink->params)) {
                            // arrays stay arrays
                            // objects are cast to associative arrays
                            $params = (array) $appData->deepLink->params;
                        } else {
                            $params = array();
                        }
                        
                        // redirect
                        return new RedirectResponse($route, $params);
                    }
                }
            } 
        }
    }// set the params if they exist
}
