<?php
namespace Chobit\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Chobit\Entity\Post;

class BlogEntityServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $db = $app['db']; // for use R::XXX static functions
        $app['model.post'] = $app->share(function() use ($app) {
            return new Post();
        });
    }
}