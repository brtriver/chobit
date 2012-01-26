<?php
namespace Chobit;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\HttpFoundation\Request;

use Chobit\Provider\InstallerServiceProvider;

class InstallerControllerProvider implements ControllerProviderInterface
{
    protected function initialize(Application $app)
    {
        // $app[model.installer]
        $app->register(new InstallerServiceProvider(), array(
            'installer.name' => $app['db.type'],
        ));
        // for template
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem(__DIR__.'/templates/installer'));
        // for create form
        $app['installer.form'] = $app['installer']->createForm($app);
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
        ->bind('installer_top');
        // confirm
        $controllers->get('/confirm', function (Application $app) {
            return $app['twig']->render('confirm.html', array());
        })
        ->bind('installer_confirm');
        // new
        $controllers->get('/new', function (Application $app) {
            return $app['twig']->render('new.html', array('form' => $app['installer.form']->createView()));
        })
        ->bind('installer_new');
        // create
        $controllers->post('/create', function (Request $request, Application $app) {
            $form = $app['installer.form'];
            if ($form->bindRequest($request)->isValid()) {
                $id = $app['installer']->writeConfigFile($form->getClientData());
                return $app['twig']->render('complete.html', array());
            } else {
                return $app['twig']->render('new.html', array('form' => $form->createView()));
            }
        })
        ->bind('installer_create');
        return $controllers;
    }
}