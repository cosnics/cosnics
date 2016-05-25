<?php
namespace Chamilo\Core\Install;

use Chamilo\Libraries\Storage\DataManager\DataSourceName;
use Doctrine\Common\ClassLoader;
use Doctrine\DBAL\DriverManager;
use HTML_QuickForm_Rule;

/**
 *
 * @package core\install
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ValidateDatabaseConnection extends HTML_QuickForm_Rule
{

    public function validate($parameters)
    {
        $classLoader = new ClassLoader('Doctrine', __DIR__ . '/../../../../configuration/plugin/');
        $classLoader->register();

        $configuration = new \Doctrine\DBAL\Configuration();
        $data_source_name = DataSourceName :: factory(
            'Doctrine',
            $parameters[0],
            $parameters[2],
            $parameters[1],
            $parameters[4],
            $parameters[3]);

        $connection_parameters = array(
            'user' => $data_source_name->get_username(),
            'password' => $data_source_name->get_password(),
            'host' => $data_source_name->get_host(),
            'driverClass' => $data_source_name->get_driver(true));

        try
        {
            DriverManager :: getConnection($connection_parameters, $configuration)->connect();
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}