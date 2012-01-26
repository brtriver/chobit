<?php
namespace Chobit;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints;

use Chobit\Provider\InstallerEntityServiceProvider;

class InstallerControllerProvider implements ControllerProviderInterface
{
    protected function initialize(Application $app)
    {
        // $app[model.installer]
        $app->register(new InstallerEntityServiceProvider());
        // for template
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem(__DIR__.'/templates/installer'));
        // for create form
        $app['installer.create.form'] = $app->share(function($app) {
            $constraint = new Constraints\Collection(array(
                'database' => new Constraints\MaxLength(array('limit'=>20)),
                'username'   => new Constraints\MaxLength(array('limit'=>20)),
                'password'  => new Constraints\MaxLength(array('limit'=>20)),
                'host'  => new Constraints\MaxLength(array('limit'=>20)),
                'prefix'  => new Constraints\MaxLength(array('limit'=>20)),
            ));
            $form = $app['form.factory']
                      ->createBuilder('form', array(), array('validation_constraint' => $constraint))
                        ->add('database', 'text', array('label' => 'Database Name (File name of SQLite):'))
                        ->add('username', 'text', array('label' => 'User Name:'))
                        ->add('password', 'password', array('label' => 'Password:'))
                        ->add('host', 'text', array('label' => 'Host Name:', 'required' => false))
                        ->add('prefix', 'text', array('label' => 'Prefix:', 'required' => false))
                      ->getForm();
            return $form;
            });
        return $app;
    }
    public function connect(Application $app)
    {
        // for template
        $app = $this->initialize($app);

        $controllers = new ControllerCollection();
        // top
        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('top.html', array());
        })
        ->bind('installer_top');;
        // confirm
        $controllers->get('/confirm', function (Application $app) {
            return $app['twig']->render('confirm.html', array());
        })
        ->bind('installer_confirm');;
        // new
        $controllers->get('/new', function (Application $app) {
            return $app['twig']->render('new.html', array('form' => $app['installer.create.form']->createView()));
        })
        ->bind('installer_new');;
        // create
        $controllers->post('/create', function (Request $request, Application $app) {
            $form = $app['installer.create.form'];
            if ($form->bindRequest($request)->isValid()) {
                $id = $app['installer']->createConfigFile($form->getClientData());
                return $app['twig']->render('complete.html', array());
            } else {
                return $app['twig']->render('new.html', array('form' => $app['installer.create.form']->createView()));
            }
        })
        ->bind('installer_create');
        return $controllers;
    }
}