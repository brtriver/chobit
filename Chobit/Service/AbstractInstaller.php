<?php
namespace Chobit\Service;

use Chobit\Service\InstallerInterface;

abstract class AbstractInstaller implements InstallerInterface {
    public $db;
    public $baseDir;
    public $configFile = 'config.php';
    public $template;
    public $form;

    public function __construct($app)
    {
        $this->baseDir = $app['base_dir'];
    }
    public function createForm($app)
    {
        throw new Exception('Cannot create input form');
    }
    protected function createParameters($params)
    {
        throw new Exception('Cannot create parameters');
    }
    public function writeConfigFile($params)
    {
        // TODO: not secure code
        $context = $this->createParameters($params);
        $path = sprintf("%s/%s", $this->baseDir, $this->configFile);
        if (is_writable($path)) {
            file_put_contents($path, $context);
        } else {
            echo "cannot write";
        }
    }
}
