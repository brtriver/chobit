<?php
namespace Chobito;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

class InstallerControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        $controllers->get('/', function (Application $app) {
            return $app->escape('This page is installer for Chobito');
        });

        return $controllers;
    }
}