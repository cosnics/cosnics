<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus\Storage;

use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Storage\DataManager\DataSourceName;
use Doctrine\DBAL\DriverManager;
use Chamilo\Application\Calendar\Extension\SyllabusPlus\Manager;

/**
 * This class represents the current CAS Account database connection.
 *
 * @author Hans De Bisschop
 */
class Connection extends \Chamilo\Libraries\Storage\DataManager\Doctrine\Connection
{

    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * The MDB2 Connection object.
     */
    protected $connection;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $dbms = PlatformSetting :: get('dbms', Manager :: context());
        $user = PlatformSetting :: get('user', Manager :: context());
        $password = PlatformSetting :: get('password', Manager :: context());
        $host = PlatformSetting :: get('host', Manager :: context());
        $database = PlatformSetting :: get('database', Manager :: context());

        $data_source_name = DataSourceName :: factory('doctrine', $dbms, $user, $host, $database, $password);
        $configuration = new \Doctrine\DBAL\Configuration();
        $connection_parameters = array(
            'dbname' => $data_source_name->get_database(),
            'user' => $data_source_name->get_username(),
            'password' => $data_source_name->get_password(),
            'host' => $data_source_name->get_host(),
            'driverClass' => $data_source_name->get_driver(true));
        $this->connection = DriverManager :: getConnection($connection_parameters, $configuration);
    }

    /**
     * Returns the instance of this class.
     *
     * @return Connection The instance.
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Gets the database connection.
     *
     * @return mixed MDB2 DB Conenction.
     */
    public function get_connection()
    {
        return $this->connection;
    }

    public function set_option($option, $value)
    {
        $this->connection->setOption($option, $value);
    }
}
