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
        // $app[model.blog]
        $app->register(new BlogEntityServiceProvider());
        // for template
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem(__DIR__.'/templates/blog'));
        // for create form
        $app['blog.create.form'] = $app->share(function($app) {
            $constraint = new Constraints\Collection(array(
                'title' => new Constraints\MaxLength(array('limit'=>10, 'message' => 'title is too long')),
                'tag'   => new Constraints\MaxLength(array('limit'=>3)),
                'article'  => new Constraints\MaxLength(array('limit'=>100)),
            ));
            $form = $app['form.factory']
                      ->createBuilder('form', array(), array('validation_constraint' => $constraint))
                        ->add('title', 'text', array('label' => 'Title:'))
                        ->add('tag', 'text', array('label' => 'Tag:'))
                        ->add('article', 'textarea', array('label' => 'Article:'))
                      ->getForm();
            return $form;
            });
        return $app;
    }
    public function connect(Application $app)
    {
        $app = $this->initialize($app);
        // contollers
        $controllers = new ControllerCollection();
        // list
        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('article/list.html', array());
        });

        // new
        $controllers->get('/new', function (Application $app) {
            return $app['twig']->render('article/new.html', array('form' => $app['blog.create.form']->createView()));
        })
        ->bind('blog_new');
        // create
        $controllers->post('/create', function (Request $request, Application $app) {
            $form = $app['blog.create.form'];
            if ($form->bindRequest($request)->isValid()) {
                $id = $app['model.blog']->store($form->getClientData());
                return $app['twig']->render('article/list.html', array());
            } else {
                return $app['twig']->render('article/new.html', array('form' => $app['blog.create.form']->createView()));
            }
        })
        ->bind('blog_create');
        return $controllers;
    }
}