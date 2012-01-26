<?php
namespace Chobit\Service\Installer;

use Symfony\Component\Validator\Constraints;
use Chobit\Service\AbstractInstaller;

class SQLite extends AbstractInstaller {
    public $configFile = 'config.php';
    public $template = <<<'EOL'
<?php
// database for SQLite
$app['db.dsn'] = 'sqlite:%%DATABASE%%';
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
            ));
            $form = $app['form.factory']
                      ->createBuilder('form', array(), array('validation_constraint' => $constraint))
                        ->add('database', 'text', array('label' => 'Database Name (File name of SQLite):'))
                        ->add('username', 'text', array('label' => 'User Name:'))
                        ->add('password', 'password', array('label' => 'Password:'))
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
}
