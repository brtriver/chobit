<?php
namespace Chobit\Service\Installer;

use Symfony\Component\Validator\Constraints;
use Chobit\Service\AbstractInstaller;

class MySQL extends AbstractInstaller {
    public $configFile = 'config.php';
    public $template = <<<'EOL'
<?php
// database for MySQL
$app['db.dsn'] = 'mysql:%%DATABASE%%';
$app['db.user'] = '%%USERNAME%%';
$app['db.password'] = '%%PASSWORD%%';
EOL;

    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function createForm($app)
    {
        $createForm = $app->share(function($app) {
            $constraint = new Constraints\Collection(array(
                'database' => new Constraints\MaxLength(array('limit'=>20)),
                'username'   => new Constraints\MaxLength(array('limit'=>20)),
                'password'  => new Constraints\MaxLength(array('limit'=>20)),
                'host'  => new Constraints\MaxLength(array('limit'=>20)),
                'prefix'  => new Constraints\MaxLength(array('limit'=>20)),
            ));
            $form = $app['form.factory']
                      ->createBuilder('form', array(), array('validation_constraint' => $constraint))
                        ->add('database', 'text', array('label' => 'Database Name (MySQL):'))
                        ->add('username', 'text', array('label' => 'User Name:'))
                        ->add('password', 'password', array('label' => 'Password:'))
                        ->add('host', 'text', array('label' => 'Host Name:', 'required' => false))
                        ->add('prefix', 'text', array('label' => 'Prefix:', 'required' => false))
                      ->getForm();
            return $form;
        });
        return $createForm;
    }
    protected function createParameters($params)
    {
        $replaces = array(
            '%%DATABASE%%' => $params['database'],
            '%%USERNAME%%' => $params['username'],
            '%%PASSWORD%%' => $params['password'],
            );
        return str_replace(array_keys($replaces), array_values($replaces), $this->template);
    }
    public function execInitSql()
    {
        $sql = file_get_contents(__DIR__.'/MySQL.sql');
        R::exec($sql);
    }
}
