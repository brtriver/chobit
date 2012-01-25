<?php
namespace Chobit\Entity;

use Silex\Application;
use Silex\ServiceProviderInterface;

class InstallerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['installer'] = $app->share(function() use ($app){
            return new InstallerEntityServiceProvider($app['base_dir']);
        });
    }
}

class InstallerEntityServiceProvider {
    public $db;
    public $baseDir;
    public $configFile = 'config.php';
    public $context = <<<'EOL'
<?php
// database
$app['db.dsn'] = 'sqlite:%%DATABASE%%';
$app['db.user'] = '%%USERNAME%%';
$app['db.password'] = '%%PASSWORD%%';
EOL;

    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }
    protected function context($params)
    {
        $replaces = array(
            '%%DATABASE%%' => $params['database'],
            '%%USERNAME%%' => $params['username'],
            '%%PASSWORD%%' => $params['password'],
            );
        return str_replace(array_keys($replaces), array_values($replaces), $this->context);
    }
    public function createConfigFile($params)
    {
        // TODO: not secure code
        $context = $this->context($params);
        $path = sprintf("%s/%s", $this->baseDir, $this->configFile);
        if (is_writable($path)) {
            file_put_contents($path, $context);
        } else {
            echo "cannot write";
        }
    }
}
