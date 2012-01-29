<?php
namespace Chobit;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints;

use Chobit\Provider\BlogEntityServiceProvider;

class BlogControllerProvider implements ControllerProviderInterface
{
    protected function initialize(Application $app)
    {
        // $app[model.post]
        $app->register(new BlogEntityServiceProvider());
        // for template
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem(array(
                                        __DIR__.'/../../templates/blog',
                                        __DIR__.'/templates/blog',
                                        )));
        return $app;
    }
    public function connect(Application $app)
    {
        $app = $this->initialize($app);
        // contollers
        $controllers = new ControllerCollection();
        // list
        $controllers->get('/', function (Application $app) {
            $posts = $app['model.post']->findAll();
            return $app['twig']->render('list.html', array('posts' => $posts));
        })
        ->bind('blog_list');
        // new post
        $controllers->get('/detail/{id}', function ($id, Application $app) {
            $create_form = $app['post.create_form'];
            $form = $create_form();
            $post = $app['model.post']->findById($id);
            return $app['twig']->render('detail.html', array('form' => $form->createView(), 'post' => $post));
        })
        ->bind('blog_detail');
        return $controllers;
    }
}