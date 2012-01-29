<?php
namespace Chobit\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use R;

class RedbeanServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['db'] = function() use ($app) {
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
            $rb = R::setup(
                $app['db.options']['dsn'],
                $app['db.options']['username'],
                $app['db.options']['password']
            );
            return $rb;
        };
    }
}