<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2;

use Exception;
use MDB2;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Connection
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Mdb2\Connection
     */
    private static $instance;

    /**
     *
     * @var MDB2
     */
    protected $connection;

    /**
     *
     * @param MDB2 $connection
     */
    private function __construct($connection = null)
    {
        if (is_null($connection))
        {
            $this->connection = self::connection_from_config();
        }
        else
        {
            $this->connection = $connection;
        }
        
        $this->connection->setOption('portability', MDB2_PORTABILITY_NONE);
        $this->connection->setCharset('utf8');
    }

    /**
     *
     * @throws Exception
     * @return MDB2
     */
    public static function connection_from_config()
    {
        $data_source_name = \Chamilo\Libraries\Storage\DataManager\DataSourceName::get_from_config('Mdb2');
        
        // The following line is for software under development, to be disabled, see below:
        $connection = MDB2::connect($data_source_name->get_connection_string(), array('debug' => 3));
        if (MDB2::isError($connection))
        {
            throw new Exception(
                'The system can not connect to the database. If you are installing, please remove the configuration file.');
        }
        // TODO: The following line is for production systems, debugging feature is disabled:
        // $this->connection = MDB2 :: connect($configuration->get_parameter('database', 'connection_string'),
        // array('debug' => 0));
        
        return $connection;
    }

    /**
     * Returns the instance of this class.
     * 
     * @return \Chamilo\Libraries\Storage\DataManager\Mdb2\Connection
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
     * @param MDB2 $connection
     * @return \Chamilo\Libraries\Storage\DataManager\Mdb2\Connection
     */
    public static function set_instance($connection)
    {
        self::$instance = new self($connection);
    }

    /**
     * Gets the database connection.
     * 
     * @return MDB2
     */
    public function get_connection()
    {
        return $this->connection;
    }

    /**
     *
     * @param MDB2 $connection
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
