<?php
#require_once __DIR__.'/silex.phar';
require_once __DIR__.'/vendor/silex.git/autoload.php';

$app = new Silex\Application();
$app['autoloader']->registerNamespace('Chobit', __DIR__);
// load setting
require_once __DIR__.'/config/config.php';
require_once __DIR__.'/config/register.php';
// mount each controller
$app->mount('/blog', new Chobit\BlogControllerProvider());
$app->mount('/install', new Chobit\InstallerControllerProvider());
// for help page
$app->get('/help', function () use ($app) {
    return  $app->escape('This page is help page');
});
// error
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }
    $error = 404 == $code ? $e->getMessage() : null;
    return new Symfony\Component\HttpFoundation\Response($app['twig']->render('error.html', array('error' => $error)), $code);
});
$app->run();