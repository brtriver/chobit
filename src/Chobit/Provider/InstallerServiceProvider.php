<?php
namespace Chobit\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class InstallerServiceProvider implements ServiceProviderInterface
{
    const DEFAULT_DATABASE = 'SQLite';
    public function register(Application $app)
    {
        if (!isset($app['base_dir'])) {
            $app['base_dir'] = __DIR__ . '/../../../';
        }
        if (!isset($app['installer.name'])) {
            $app['installer.name'] = self::DEFAULT_DATABASE;
        }
        $app['installer'] = $app->share(function() use ($app){
            $class = 'Chobit\\Service\\Installer\\' . $app['installer.name'];
            return new $class($app);
        });
    }
}