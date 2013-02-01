<?php

namespace Nerd\FacebookBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * This listener's job is to redirect to the appropriate 
 * page if this is a deep link. Deep links are achieved 
 * by utilizing facebook's app_data parameter.
 *
 * Facebook passes app_data into our app via the signed request.
 *
 * See Nerd Facebook Helper's getPageTabDeepLinkUrl method to see how
 * the url is constructed.
 */

class ResponseListener
{
    protected $_facebookHelper;

    public function __construct($facebookHelper) 
    {
        $this->_facebookHelper = $facebookHelper;

        // force the parsing and storage of the access token before any redirects
        $this->_facebookHelper->getAccessToken();
    }
    
    public function onKernelResponse(FilterResponseEvent $event)
    {

        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {

            // get the signed request.
            $signedRequest = $this->_facebookHelper->getSignedRequest(); 

            // check for the presence of app_data in the signed request
            if (! empty($signedRequest['app_data'])) {

                $appData = json_decode($signedRequest['app_data']);
                
                // proceed if app_data is a json object with deepLink property
                if (is_object($appData) && ! empty($appData->deepLink)) {

                    // does deepLink have name and route properties?
                    if (! empty($appData->deepLink->name)
                        && ! empty($appData->deepLink->pattern)
                        && ! empty($appData->deepLink->defaults)) {

                        $name = $appData->deepLink->name;
                        $pattern = $appData->deepLink->pattern;
                        $defaults = (array) $appData->deepLink->defaults;
                        $route = new Route($pattern, $defaults);

                        $routes = new RouteCollection();
                        $routes->add($name, $route);

                        // redirect
                        $context = new RequestContext($_SERVER['SCRIPT_NAME']);
                        $generator = new UrlGenerator($routes, $context);
                        $url = $generator->generate($name);

                        $response = new RedirectResponse($url);
                        $response->send();
                    }
                }
            } 
        }
    }
}
