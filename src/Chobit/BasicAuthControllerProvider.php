<?php
namespace Chobit;

use Silex\Application;
use Silex\SilexEvents;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class BasicAuthControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // init
        $app['basic_auth.options'] = array_replace(array(
            'username'      => 'demo',
            'password'      => '123456',
            'redirect'      => 'home',
            'no_secured_paths'=> array(),
        ), isset($app['basic_auth.options']) ? $app['basic_auth.options'] : array());
        $app['basic_auth.response'] = function() {
            $response = new Response();
            $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Basic Login'));
            $response->setStatusCode(401, 'Please sign in.');
            return $response;            
        };
        // controllers
        $controllers = new ControllerCollection();
        // login
        $controllers->get('/', function (Request $request, Application $app) {
            $username = $request->server->get('PHP_AUTH_USER', false);
            $password = $request->server->get('PHP_AUTH_PW');

            if ($app['basic_auth.options']['username'] === $username && $app['basic_auth.options']['password'] === $password) {
                $app['session']->set('isAuthenticated', true);
                return $app->redirect($app['url_generator']->generate($app['basic_auth.options']['redirect']));
            }
            return $app['basic_auth.response'];
        })->bind('login');

        // logout
        $controllers->get('/logout', function (Request $request, Application $app) {
            $app['session']->set('isAuthenticated', false);
            return $app['basic_auth.response'];
        })->bind('logout');

        // add befre event
        $this->addCheckAuthEvent($app);
        return $controllers;
    }

    private function addCheckAuthEvent($app)
    {
        // check login
        $app['dispatcher']->addListener(SilexEvents::BEFORE, function (GetResponseEvent $event) use ($app){
            $request = $event->getRequest();
            $paths = array_merge($app['basic_auth.options']['no_secured_paths'], array($app['url_generator']->generate('login')));
            foreach ($paths as $path) {
                if (preg_match("#" . $path .".*$#is", $request->getRequestUri())) {
                    return;
                }
            }
            $app['session']->get('isAuthenticated');
            if (!$app['session']->get('isAuthenticated')) {
                $ret = $app->redirect($app['url_generator']->generate('login'));
            } else {
                $ret = null;
            }
            if ($ret instanceof Response) {
                $event->setResponse($ret);
            }
        }, 0);
    }
}