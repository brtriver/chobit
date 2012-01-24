<?php
namespace Chobito;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\Extension\Csrf\Type\CsrfType;

class BlogControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();
        // list
        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('blog/list.html', array());
        });
        // new
        $form_post = function($app) {
          return  $app['form.factory']
            	        ->createBuilder('form')
                	    ->add('title', 'text', array('label' => 'Title:'))
                	    ->add('tag', 'text', array('label' => 'Tag:'))
                	    ->add('article', 'textarea', array('label' => 'Article:'))
            	        ->getForm();            
        };
        $controllers->get('/new', function (Application $app) use ($form_post) {
           $form = $form_post($app);
            return $app['twig']->render('blog/new.html', array('form' => $form->createView()));
        });
        $controllers->post('/create', function (Request $request, Application $app) use ($form_post) {
            $form = $request->get('form');
print_r($form);
            $collectionConstraint = new Constraints\Collection(array(
                'title' => new Constraints\MaxLength(array('limit'=>3, 'message' => 'Invalid email address')),
                'tag'   => new Constraints\MaxLength(array('limit'=>3)),
                'article'  => new Constraints\MaxLength(array('limit'=>3)),
                '_token' => new Constraints\Callback(array('methods' => array(function(){
                    return false;
                })))
            ));
            
            $errorList = $app['validator']->validateValue($form, $collectionConstraint);
            $errors = array();
            foreach ($errorList as $error) {
                // getPropertyPath returns form [email], so we strip it
                $field = substr($error->getPropertyPath(), 1, -1);

                $errors[$field] = $error->getMessage();
            }

echo "<pre>";
print_r($errors);

            return;
        })
        ->bind('blog_create');
        return $controllers;
    }
}