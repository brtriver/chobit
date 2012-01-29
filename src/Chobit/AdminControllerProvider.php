<?php
namespace Chobit;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints;

use Chobit\Provider\BlogEntityServiceProvider;

class AdminControllerProvider implements ControllerProviderInterface
{
    protected function initialize(Application $app)
    {
        // $app[model.post]
        $app->register(new BlogEntityServiceProvider());
        // for template
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem(__DIR__.'/templates/admin'));
        // for create form
        $app['post.create_form'] = $app->protect(function($bind=array()) use($app){
            $constraint = new Constraints\Collection(array(
                'fields' => array(
                    'title' => new Constraints\MaxLength(array('limit'=>10, 'message' => 'title is too long')),
                    'content'  => new Constraints\MaxLength(array('limit'=>100)),
                ),
                'allowExtraFields' => true, // for id when edit the post
            ));
            $form = $app['form.factory']
                      ->createBuilder('form', $bind, array('validation_constraint' => $constraint))
                        ->add('title', 'text', array('label' => 'Title:'))
                        ->add('content', 'textarea', array('label' => 'Content:'))
                        ->add('id', 'hidden', array())
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
            $posts = $app['model.post']->findAll();
            return $app['twig']->render('post/list.html', array('posts' => $posts));
        })
        ->bind('post_list');
        // new post
        $controllers->get('/new', function (Application $app) {
            $create_form = $app['post.create_form'];
            $form = $create_form();
            return $app['twig']->render('post/new.html', array('form' => $form->createView()));
        })
        ->bind('post_new');
        // create post
        $controllers->post('/create', function (Request $request, Application $app) {
            $create_form = $app['post.create_form'];
            $form = $create_form();
            if ($form->bindRequest($request)->isValid()) {
                $id = $app['model.post']->store($form->getClientData());
                return $app->redirect($app['url_generator']->generate('post_list'));
            } else {
                return $app['twig']->render('post/new.html', array('form' => $form->createView()));
            }
        })
        ->bind('post_create');
        // edit post
        $controllers->get('/edit/{id}', function ($id, Application $app) {
            $post = $app['model.post']->findById($id);
            $create_form = $app['post.create_form'];
            $form = $create_form($post);
            return $app['twig']->render('post/edit.html', array('form' => $form->createView()));
        })
        ->bind('post_edit');
        // updated post
        $controllers->post('/update', function (Request $request, Application $app) {
            $params = $request->get('form');
            $create_form = $app['post.create_form'];
            $form = $create_form();
            if ($app['model.post']->isExist($params['id']) && $form->bindRequest($request)->isValid()) {
                $id = $app['model.post']->update($params['id'], $form->getClientData());
                return $app->redirect($app['url_generator']->generate('post_list'));
            } else {
                return $app['twig']->render('post/edit.html', array('form' => $form->createView()));
            }
        })
        ->bind('post_update');
        // delete post
        $controllers->get('/delete/{id}', function ($id, Application $app) {
            $app['model.post']->delete($id);
            return $app->redirect($app['url_generator']->generate('post_list'));
        })
        ->bind('post_delete');
        return $controllers;
    }
}