<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\Exception\ConnectionException;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConnectionFactory
{

    private DataSourceName $dataSourceName;

    public function __construct(DataSourceName $dataSourceName)
    {
        $this->dataSourceName = $dataSourceName;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    public function getConnection(): Connection
    {
        $dataSourceName = $this->getDataSourceName();

        $connectionParameters = array(
            'dbname' => $dataSourceName->getDatabase(),
            'user' => $dataSourceName->getUsername(),
            'password' => $dataSourceName->getPassword(),
            'host' => $dataSourceName->getHost(),
            'driverClass' => $dataSourceName->getDriver(true),
            'charset' => 'UTF8'
        );

        try
        {
            return DriverManager::getConnection($connectionParameters, new Configuration());
        }
        catch (Exception $exception)
        {
            throw new ConnectionException(
                'Could not connect to the database. Please contact your system administrator.'
            );
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName
     */
    public function getDataSourceName(): DataSourceName
    {
        return $this->dataSourceName;
    }

    public function setDataSourceName(DataSourceName $dataSourceName): ConnectionFactory
    {
        $this->dataSourceName = $dataSourceName;

        return $this;
    }
}
