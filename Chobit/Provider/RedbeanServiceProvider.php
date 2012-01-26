<?php
namespace Chobit\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class RedbeanServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['db'] = $app->share(function() use ($app) {
            if (isset($app['db.redbean.class_path'])) {
                $redbean_path = $app['db.redbean.class_path'] . '/rb.php';
                include_once $redbean_path;
            }
            $default_options = array(
                'dsn'      => null,
                'username' => null,
                'password' => null,
                'frozen'   => false,
            );
            $app['db.options'] = array_merge($default_options, $app['db.options']);
            $toolbox = \RedBean_Setup::kickstart(
                $app['db.options']['dsn'],
                $app['db.options']['username'],
                $app['db.options']['password'],
                $app['db.options']['frozen']);
            return $toolbox->getRedBean();
        });
    }
}
