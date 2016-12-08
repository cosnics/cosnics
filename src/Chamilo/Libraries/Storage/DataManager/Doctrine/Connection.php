<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Doctrine\DBAL\DriverManager;
use Chamilo\Libraries\Storage\Exception\ConnectionException;

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
     */
    private function __construct($connection = null)
    {
        if (is_null($connection))
        {
            $data_source_name = \Chamilo\Libraries\Storage\DataManager\DataSourceName::get_from_config('Doctrine');
            $configuration = new \Doctrine\DBAL\Configuration();
            $connection_parameters = array(
                'dbname' => $data_source_name->get_database(),
                'user' => $data_source_name->get_username(),
                'password' => $data_source_name->get_password(),
                'host' => $data_source_name->get_host(),
                'driverClass' => $data_source_name->get_driver(true),
                'charset' => 'UTF8');
            $this->connection = DriverManager::getConnection($connection_parameters, $configuration);

            try
            {
                $this->connection->connect();
            }
            catch (\Exception $ex)
            {
                throw new ConnectionException(
                    'Could not connect to the database. Please contact your system administrator.');
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
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
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

    /**
     *
     * @param string $option
     * @param string $value
     */
    public function set_option($option, $value)
    {
        $this->connection->setOption($option, $value);
    }
}
