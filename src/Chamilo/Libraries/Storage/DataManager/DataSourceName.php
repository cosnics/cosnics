<?php
namespace Chamilo\Libraries\Storage\DataManager;

/**
 * Describes a generic database-backed storage layer connection string
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DataSourceName
{
    const DRIVER_OCI8 = 'oci8';
    const DRIVER_PGSQL = 'pgsql';
    const DRIVER_SQLITE = 'sqlite';
    const DRIVER_MYSQL = 'mysql';
    const DRIVER_MSSQL = 'mssql';
    const DRIVER_INTERBASE = 'interbase';
    const DRIVER_IBM_DB2 = 'ibm_db2';
    const DRIVER_IBM = 'ibm';
    const DRIVER_OCI = 'oci';

    /**
     *
     * @var string[]
     */
    private $settings;

    /**
     * The database driver to be used
     *
     * @var string
     */
    private $driver;

    /**
     * The username to be used to make the connection
     *
     * @var string
     */
    private $username;

    /**
     * The password to be used to make the connection
     *
     * @var string
     */
    private $password;

    /**
     * The host to be used to make the connection
     *
     * @var string
     */
    private $host;

    /**
     * The port to be used to make the connection
     *
     * @var string
     */
    private $port;

    /**
     * The database we want to connect to
     *
     * @var string
     */
    private $database;

    /**
     * Constructor
     *
     * @param string[] $settings
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->driver = $settings['driver'];
        $this->username = $settings['username'];
        $this->password = isset($settings['password']) ? $settings['password'] : null;
        $this->host = $settings['host'];
        $this->port = isset($settings['port']) ? $settings['port'] : null;
        $this->database = $settings['name'];
    }

    /**
     *
     * @return string[]
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get the database driver to be used
     *
     * @return string
     */
    public function get_driver($implementation = false)
    {
        if ($implementation)
        {
            return $this->get_implemented_driver();
        }
        else
        {
            return $this->driver;
        }
    }

    /**
     * Set the database driver to be used
     *
     * @param string $driver
     */
    public function set_driver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the username to be used to make the connection
     *
     * @return string
     */
    public function get_username()
    {
        return $this->username;
    }

    /**
     * Set the username to be used to make the connection
     *
     * @param string $username
     */
    public function set_username($username)
    {
        $this->username = $username;
    }

    /**
     * Get the password to be used to make the connection
     *
     * @return string
     */
    public function get_password()
    {
        return $this->password;
    }

    /**
     * Set the password to be used to make the connection
     *
     * @param string $password
     */
    public function set_password($password)
    {
        $this->password = $password;
    }

    /**
     * Get the host to be used to make the connection
     *
     * @return string
     */
    public function get_host()
    {
        return $this->host;
    }

    /**
     * Set the host to be used to make the connection
     *
     * @param string $host
     */
    public function set_host($host)
    {
        $this->host = $host;
    }

    /**
     * Get the port to be used to make the connection
     *
     * @return string
     */
    public function get_port()
    {
        return $this->port;
    }

    /**
     * Set the port to be used to make the connection
     *
     * @param string $port
     */
    public function set_port($port)
    {
        $this->port = $port;
    }

    /**
     * Get the database we want to connect to
     *
     * @return string
     */
    public function get_database()
    {
        return $this->database;
    }

    /**
     * Set the database we want to connect to
     *
     * @param string $database
     */
    public function set_database($database)
    {
        $this->database = $database;
    }

    /**
     * Parse a string to a valid data source name
     *
     * @param string $type
     * @return \Chamilo\Libraries\Storage\DataManager\DataSourceName
     */
    public static function get_from_config($type)
    {
        return self::factory(
            $type,
            array(
                'driver' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'driver'),
                'username' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'username'),
                'host' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'host'),
                'name' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'name'),
                'password' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'password')));
    }

    /**
     * Parse a string to a valid data source name
     *
     * @param string $type
     * @param string $connection_string
     * @return \Chamilo\Libraries\Storage\DataManager\DataSourceName
     */
    public static function parse($type, $connection_string)
    {
        $data_source_name = self::factory($type);

        // Find driver
        if (($position = strpos($connection_string, '://')) !== false)
        {
            $string = substr($connection_string, 0, $position);
            $connection_string = substr($connection_string, $position + 3);
        }
        else
        {
            $string = $connection_string;
            $connection_string = null;
        }

        // Get the driver
        if (preg_match('|^(.+?)\((.*?)\)$|', $string, $arr))
        {
            $data_source_name->set_driver($arr[1]);
        }
        else
        {
            $data_source_name->set_driver($string);
        }

        if (! count($connection_string))
        {
            throw new \Exception('The connection string passed to the DataSourceName :: parse() method is not valid');
        }

        // Get (if found): username and password
        // $connection_string => username:password@protocol+host_specification/database
        if (($at = strrpos($connection_string, '@')) !== false)
        {
            $string = substr($connection_string, 0, $at);
            $connection_string = substr($connection_string, $at + 1);
            if (($position = strpos($string, ':')) !== false)
            {
                $data_source_name->set_username(rawurldecode(substr($string, 0, $position)));
                $data_source_name->set_password(rawurldecode(substr($string, $position + 1)));
            }
            else
            {
                $data_source_name->set_username(rawurldecode($string));
            }
        }

        // Find protocol and host specification

        // $connection_string => protocol(protocol_options)/database
        if (preg_match('|^([^(]+)\((.*?)\)/?(.*?)$|', $connection_string, $match))
        {
            $protocol = $match[1];
            $protocol_options = $match[2] ? $match[2] : false;
            $connection_string = $match[3];

            // $connection_string => protocol+hostspec/database (old format)
        }
        else
        {
            if (strpos($connection_string, '+') !== false)
            {
                list($protocol, $connection_string) = explode('+', $connection_string, 2);
            }
            if (strpos($connection_string, '//') === 0 && strpos($connection_string, '/', 2) !== false &&
                 $data_source_name->get_driver() == 'oci8')
            {
                // oracle's "Easy Connect" syntax: "username/password@[//]host[:port][/service_name]"
                // e.g. "scott/tiger@//mymachine:1521/oracle"
                $protocol_options = $connection_string;
                $connection_string = substr($protocol_options, strrpos($protocol_options, '/') + 1);
            }
            elseif (strpos($connection_string, '/') !== false)
            {
                list($protocol_options, $connection_string) = explode('/', $connection_string, 2);
            }
            else
            {
                $protocol_options = $connection_string;
                $connection_string = null;
            }
        }

        // process the different protocol options
        $protocol_options = rawurldecode($protocol_options);
        if (strpos($protocol_options, ':') !== false)
        {
            list($protocol_options, $port) = explode(':', $protocol_options);
        }
        else
        {
            $port = null;
        }

        $data_source_name->set_host($protocol_options);
        $data_source_name->set_port($port);

        // Get database if there is one: $connection_string => database
        if ($connection_string)
        {
            // /database
            if (($position = strpos($connection_string, '?')) === false)
            {
                $data_source_name->set_database($connection_string);
            }
            // /database?param1=value1&param2=value2
            else
            {
                $data_source_name->set_database(substr($connection_string, 0, $position));

                /*
                 * Ignore the following for now
                 */

                // $connection_string = substr($connection_string, $position + 1);
                // if (strpos($connection_string, '&') !== false)
                // {
                // $opts = explode('&', $connection_string);
                // }
                // else
                // { // database?param1=value1
                // $opts = array($connection_string);
                // }
                // foreach ($opts as $opt)
                // {
                // list($key, $value) = explode('=', $opt);
                // if (! isset($parsed[$key]))
                // {
                // // don't allow params overwrite
                // $parsed[$key] = rawurldecode($value);
                // }
                // }
            }
        }

        return $data_source_name;
    }

    /**
     * Is the connection string valid for the implementation? Basically checks if the required fields are available.
     *
     * @return boolean
     */
    public function is_valid()
    {
        $driver = $this->get_driver();
        if (! isset($driver))
        {
            return false;
        }

        $username = $this->get_username();
        if (! isset($username))
        {
            return false;
        }

        $host = $this->get_host();
        if (! isset($host))
        {
            return false;
        }

        $database = $this->get_database();
        if (! isset($database))
        {
            return false;
        }

        return true;
    }

    /**
     * Return the actual name of the implementation in a specific storage layer implementation
     *
     * @return string
     */
    abstract public function get_implemented_driver();

    /**
     * Factory to instantiate the correct type of DataSourceName
     *
     * @param string[] $settings
     * @return \Chamilo\Libraries\Storage\DataManager\DataSourceName
     */
    public static function factory($type, $settings)
    {
        $class = __NAMESPACE__ . '\\' . $type . '\DataSourceName';
        return new $class($settings);
    }

    /**
     *
     * @return string
     */
    public function get_connection_string()
    {
        $string = array();

        $string[] = $this->get_driver(true);
        $string[] = '://';
        $string[] = $this->get_username();
        $string[] = ':';
        $string[] = $this->get_password();
        $string[] = '@';
        $string[] = $this->get_host();
        if ($this->get_port())
        {
            $string[] = ':';
            $string[] = $this->get_port();
        }
        $string[] = '/';
        $string[] = $this->get_database();

        return implode('', $string);
    }
}
