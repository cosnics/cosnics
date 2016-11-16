<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\Exception\ConnectionException;
use Doctrine\DBAL\DriverManager;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConnectionFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName
     */
    private $dataSourceName;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName $dataSourceName
     */
    public function __construct(DataSourceName $dataSourceName)
    {
        $this->dataSourceName = $dataSourceName;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName
     */
    public function getDataSourceName()
    {
        return $this->dataSourceName;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName $dataSourceName
     */
    public function setDataSourceName($dataSourceName)
    {
        $this->dataSourceName = $dataSourceName;
    }

    /**
     *
     * @throws \Exception
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        $dataSourceName = $this->getDataSourceName();

        $connectionParameters = array(
            'dbname' => $dataSourceName->get_database(),
            'user' => $dataSourceName->get_username(),
            'password' => $dataSourceName->get_password(),
            'host' => $dataSourceName->get_host(),
            'driverClass' => $dataSourceName->get_driver(true),
            'charset' => 'UTF8');

        $connection = DriverManager::getConnection($connectionParameters, new \Doctrine\DBAL\Configuration());

        try
        {
            return $connection;
        }
        catch (\Exception $exception)
        {
            throw new ConnectionException('Could not connect to the database. Please contact your system administrator.');
        }
    }
}
