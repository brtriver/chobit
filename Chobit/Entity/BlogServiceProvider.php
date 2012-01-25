<?php
namespace Chobit\Entity;

use Silex\Application;
use Silex\ServiceProviderInterface;

class BlogServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['model.blog'] = $app->share(function() use ($app) {
            return new BlogEntityServiceProvider($app['db']);
        });
    }
}

class BlogEntityServiceProvider {
    public $db;
    public function __construct($db)
    {
        $this->db = $db;

    }
    public function store($params)
    {
        $article = $this->db->dispense('article');
        $article->title = $params['title'];
        $article->tag = $params['tag'];
        $article->article = $params['article'];
        $id = $this->db->store($article);
        return $id;
    }
}
