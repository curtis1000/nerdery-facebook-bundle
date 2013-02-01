<?php

namespace Nerd\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class JavascriptRedirectController extends Controller
{
    /**
     * @Route("/javascript-redirect", name="nerd_facebook_javascript_redirect")
     * @Template()
     */
    public function indexAction($redirectUrl)
    {
        return array('redirectUrl' => $redirectUrl);
    }

}
