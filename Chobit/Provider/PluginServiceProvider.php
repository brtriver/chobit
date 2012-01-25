<?php
namespace Chobit\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class PluginServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['plugin'] = $app->share(function() use ($app) {
            return null;
        });
    }
}
