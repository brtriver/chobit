<?php
// $app['twig']
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => $app['base_dir'].'/templates',
    'twig.class_path' => $app['base_dir'].'/vendor/Twig.git/lib',
));
// $app['form']
$app->register(new Silex\Provider\FormServiceProvider(), array(
    'form.class_path' => $app['base_dir'].'/vendor/symfony.git/src',
));
// $app['bridge]
$app->register(new Silex\Provider\SymfonyBridgesServiceProvider(), array(
    'symfony_bridges.class_path'  => $app['base_dir'].'/vendor/symfony.git/src',
));
// $app['translator']
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translation.class_path' => $app['base_dir'] . '/vendor/symfony.git/src',
    'translator.messages' => array()
));
// $app['url_generator']
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
// $app['session']
$app->register(new Silex\Provider\SessionServiceProvider());
// $app['validator']
$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.class_path' => $app['base_dir'] . '/vendor/symfony.git/src',
));

// $app['db']
$app->register(new Chobit\Provider\RedbeanServiceProvider(), array(
    'db.redbean.class_path' => realpath($app['base_dir'] . '/vendor/redbean.git'),
    'db.options' => array(
        'dsn' => sprintf('%s', $app['db.dsn']),
        'username' => $app['db.user'],
        'password' => $app['db.password'],
    ),
));
