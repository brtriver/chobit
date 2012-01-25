<?php
#require_once __DIR__.'/silex.phar';
require_once __DIR__.'/vendor/silex.git/autoload.php';

$app = new Silex\Application();
$app['autoloader']->registerNamespace('Chobito', __DIR__);
// $app['twig']
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__.'/templates',
    'twig.class_path' => __DIR__.'/vendor/Twig.git/lib',
));
// $app['form']
$app->register(new Silex\Provider\FormServiceProvider(), array(
    'form.class_path' => __DIR__.'/vendor/symfony.git/src',
));
// $app['bridge]
$app->register(new Silex\Provider\SymfonyBridgesServiceProvider(), array(
    'symfony_bridges.class_path'  => __DIR__.'/vendor/symfony.git/src',
));
// $app['translator']
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translation.class_path' => __DIR__ . '/vendor/symfony.git/src',
    'translator.messages' => array()
));
// $app['url_generator']
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
// $app['session']
$app->register(new Silex\Provider\SessionServiceProvider());
// $app['validator']
$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.class_path' => __DIR__ . '/vendor/symfony.git/src',
));

// $app['db']
$app['db.dsn'] = 'sqlite:/tmp/dbfile.txt';
$app['db.user'] = 'chobit';
$app['db.password'] = '';
$app->register(new Chobito\Provider\RedbeanServiceProvider(), array(
    'db.redbean.class_path' => realpath(__DIR__ . '/vendor/redbean.git'),
    'db.options' => array(
        'dsn' => sprintf('%s', $app['db.dsn']),
        'username' => $app['db.user'],
        'password' => $app['db.password'],
    ),
));

// mount each controller
$app->mount('/blog', new Chobito\BlogControllerProvider());
$app->mount('/install', new Chobito\InstallerControllerProvider());
// for debug
$app['debug'] = true;
// for help page
$app->get('/help', function () use ($app) {
    return  $app->escape('This page is help page');
});

$app->run();