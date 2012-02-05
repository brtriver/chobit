<?php
// for debug
$app['debug'] = true;
// path
$app['base_dir'] = __DIR__ . '/..';
// database type
$app['db.type'] = 'SQLite'; // SQLite, MySQL
// database
$app['db.dsn'] = 'sqlite:/tmp/dbfile.txt';
$app['db.user'] = 'chobit';
$app['db.password'] = '';
// session
$app['basic_auth.options'] = array(
    'username' => 'chobit',
    'password' => 'chobit',
    'redirect' => 'post_list',
    'no_secured_paths' =>  array('^.*/blog.*'),
);
// twig
$app['twig.form.templates'] = array('form/form_twitter_bootstrap_layout.hrml.twig');
$externalConfigFile = $app['base_dir'] . '/config.php'; 
if (file_exists($externalConfigFile)) {
    include $externalConfigFile;
}