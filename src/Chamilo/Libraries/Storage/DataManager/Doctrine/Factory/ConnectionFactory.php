<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\Exception\ConnectionException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Exception;

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
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getConnection()
    {
        $dataSourceName = $this->getDataSourceName();

        $connectionParameters = array(
            'dbname' => $dataSourceName->getDatabase(), 'user' => $dataSourceName->getUsername(),
            'password' => $dataSourceName->getPassword(), 'host' => $dataSourceName->getHost(),
            'driverClass' => $dataSourceName->getDriver(true), 'charset' => 'UTF8'
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
}
