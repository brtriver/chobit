<?php
namespace Chobit\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Chobit\Entity\Installer;

class InstallerEntityServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['installer'] = $app->share(function() use ($app){
            return new Installer($app['base_dir']);
        });
    }
}