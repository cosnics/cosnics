<?php
namespace Chamilo\Core\Install;

use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
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
        $configuration = new \Doctrine\DBAL\Configuration();

        $settings = array(
            'driver' => $parameters[0],
            'username' => $parameters[2],
            'password' => $parameters[3],
            'host' => $parameters[1],
            'name' => $parameters[4],
            'charset' => 'utf8');

        $dataSourceName = new DataSourceName($settings);

        $connectionFactory = new ConnectionFactory($dataSourceName);

        try
        {
            $connectionFactory->getConnection();
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}