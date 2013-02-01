##Setup##
Check this repository out to src/Nerd/FacebookBundle.  

Add the following configuration entries to your app config:

    parameters:  
        nerd_facebook.appId: yourAppIdGoesHere  
        nerd_facebook.secret: yourAppSecretGoesHere  
        nerd_facebook.cookie: "true"
        nerd_facebook.scope: extended,permissions,go,here
    
    twig:
        globals:
            appId: %nerd_facebook.appId%  
            cookie: %nerd_facebook.cookie%  
            scope: %nerd_facebook.scope%  
  
    assetic:  
        bundles: [NerdFacebookBundle]  
     
Check out the Facebook PHP SDK (https://github.com/facebook/facebook-php-sdk) to:  
/vendor/facebook/php-sdk

This bundle makes use of the Assetic Bundle in order to make assets within this bundle publicly accessible. To enable this functionality, run the following on the command line:  
    
    php app/console assets:install --symlink
    
##Usage##

...will fill out...
