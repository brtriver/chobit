<?php
namespace Chobit\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Chobit\Entity\Blog;

class BlogEntityServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['model.blog'] = $app->share(function() use ($app) {
            return new Blog($app['db']);
        });
    }
}