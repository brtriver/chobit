<?php
namespace Chobito;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\Extension\Csrf\Type\CsrfType;

use Chobito\Entity;

class BlogControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // $app[model.blog]
        $app->register(new Entity\BlogServiceProvider());
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
        // contollers
        $controllers = new ControllerCollection();
        // list
        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('blog/list.html', array());
        });

        // new
        $controllers->get('/new', function (Application $app) {
                return $app['twig']->render('blog/new.html', array('form' => $app['blog.create.form']->createView()));
        })
        ->bind('blog_new');
        // create
        $controllers->post('/create', function (Request $request, Application $app) {
             $is_valid = $app['blog.create.is_valid'];
             if ($app['blog.create.form']->bindRequest($request)->isValid()) {
                 $id = $app['model.blog']->store($request->get('form'));
                 return $id;
             } else {
             }
             return $app['twig']->render('blog/new.html', array('form' => $app['blog.create.form']->createView()));
        })
        ->bind('blog_create');
        return $controllers;
    }
}