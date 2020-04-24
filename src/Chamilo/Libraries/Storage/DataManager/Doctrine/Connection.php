<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Chamilo\Libraries\Storage\Exception\ConnectionException;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the service-based ConnectionFactory
 */
class Connection
{

    /**
     * Instance of this class for the singleton pattern.
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Connection
     */
    private static $instance;

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     *
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    private function __construct($connection = null)
    {
        if (is_null($connection))
        {
            $dataSourceName = \Chamilo\Libraries\Storage\DataManager\DataSourceName::getFromConfiguration('Doctrine');
            $configuration = new Configuration();
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
                $this->connection = DriverManager::getConnection($connectionParameters, $configuration);
                $this->connection->connect();
            }
            catch (Exception $ex)
            {
                throw new ConnectionException(
                    'Could not connect to the database. Please contact your system administrator.'
                );
            }
        }
        else
        {
            $this->connection = $connection;
        }
    }

    /**
     * Returns the instance of this class.
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Connection
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     *
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    public static function set_instance($connection)
    {
        self::$instance = new self($connection);
    }

    /**
     * Gets the database connection.
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function get_connection()
    {
        return $this->connection;
    }

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function set_connection($connection)
    {
        $this->connection = $connection;
    }
}
