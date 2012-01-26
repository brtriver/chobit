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

$externalConfigFile = $app['base_dir'] . '/config.php'; 
if (file_exists($externalConfigFile)) {
    include $externalConfigFile;
}