<?php
namespace Chamilo\Libraries\Storage\DataManager;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

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
     *
     * @var string
     */
    private $charset;

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
        $this->charset = $settings['charset'];
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
     * @param boolean $implementation
     * @return string
     * @deprecated Use getDriver() now
     */
    public function get_driver($implementation = false)
    {
        return $this->getDriver($implementation);
    }

    /**
     * Get the database driver to be used
     *
     * @param boolean $implementation
     * @return string
     */
    public function getDriver($implementation = false)
    {
        if ($implementation)
        {
            return $this->getImplementedDriver();
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
     * @deprecated Use setDriver() now
     */
    public function set_driver($driver)
    {
        $this->setDriver($driver);
    }

    /**
     * Set the database driver to be used
     *
     * @param string $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the username to be used to make the connection
     *
     * @return string
     * @deprecated Use getUsername() now
     */
    public function get_username()
    {
        return $this->getUsername();
    }

    /**
     * Get the username to be used to make the connection
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the username to be used to make the connection
     *
     * @param string $username
     * @deprecated Use setUsername() now
     */
    public function set_username($username)
    {
        $this->setUsername($username);
    }

    /**
     * Set the username to be used to make the connection
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get the password to be used to make the connection
     *
     * @return string
     * @deprecated Use getPassword() now
     */
    public function get_password()
    {
        return $this->getPassword();
    }

    /**
     * Get the password to be used to make the connection
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the password to be used to make the connection
     *
     * @param string $password
     * @deprecated Use setPassword() now
     */
    public function set_password($password)
    {
        $this->setPassword($password);
    }

    /**
     * Set the password to be used to make the connection
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get the host to be used to make the connection
     *
     * @return string
     * @deprecated Use getHost() now
     */
    public function get_host()
    {
        return $this->getHost();
    }

    /**
     * Get the host to be used to make the connection
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the host to be used to make the connection
     *
     * @param string $host
     * @deprecated Use setHost() now
     */
    public function set_host($host)
    {
        $this->setHost($host);
    }

    /**
     * Set the host to be used to make the connection
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Get the port to be used to make the connection
     *
     * @return string
     * @deprecated Use getPort() now
     */
    public function get_port()
    {
        return $this->getPort();
    }

    /**
     * Get the port to be used to make the connection
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the port to be used to make the connection
     *
     * @param string $port
     * @deprecated Use setPort() now
     */
    public function set_port($port)
    {
        $this->setPort($port);
    }

    /**
     * Set the port to be used to make the connection
     *
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Get the database we want to connect to
     *
     * @return string
     * @deprecated Use getDatabase() now
     */
    public function get_database()
    {
        return $this->getDatabase();
    }

    /**
     * Get the database we want to connect to
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set the database we want to connect to
     *
     * @param string $database
     * @deprecated Use setDatabase() now
     */
    public function set_database($database)
    {
        $this->setDatabase($database);
    }

    /**
     * Set the database we want to connect to
     *
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Parse a string to a valid data source name
     *
     * @param string $type
     * @return \Chamilo\Libraries\Storage\DataManager\DataSourceName
     * @deprecated Use getFromConfiguration() now
     */
    public static function get_from_config($type)
    {
        return static::getFromConfiguration($type);
    }

    /**
     * Parse a string to a valid data source name
     *
     * @param string $type
     * @return \Chamilo\Libraries\Storage\DataManager\DataSourceName
     */
    public static function getFromConfiguration($type)
    {
        $fileConfigurationConsulter = new ConfigurationConsulter(
            new FileConfigurationLoader(
                new FileConfigurationLocator(new PathBuilder(new ClassnameUtilities(new StringUtilities())))));

        return self::factory(
            $type,
            array(
                'driver' => $fileConfigurationConsulter->getSetting(
                    array('Chamilo\Configuration', 'database', 'driver')),
                'username' => $fileConfigurationConsulter->getSetting(
                    array('Chamilo\Configuration', 'database', 'username')),
                'host' => $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'database', 'host')),
                'name' => $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'database', 'name')),
                'password' => $fileConfigurationConsulter->getSetting(
                    array('Chamilo\Configuration', 'database', 'password'))));
    }

    /**
     * Parse a string to a valid data source name
     *
     * @param string $type
     * @param string $connectionString
     * @return \Chamilo\Libraries\Storage\DataManager\DataSourceName
     */
    public static function parse($type, $connectionString)
    {
        $dataSourceName = self::factory($type);

        // Find driver
        if (($position = strpos($connectionString, '://')) !== false)
        {
            $string = substr($connectionString, 0, $position);
            $connectionString = substr($connectionString, $position + 3);
        }
        else
        {
            $string = $connectionString;
            $connectionString = null;
        }

        // Get the driver
        if (preg_match('|^(.+?)\((.*?)\)$|', $string, $arr))
        {
            $dataSourceName->setDriver($arr[1]);
        }
        else
        {
            $dataSourceName->setDriver($string);
        }

        if (! count($connectionString))
        {
            throw new \Exception('The connection string passed to the DataSourceName :: parse() method is not valid');
        }

        // Get (if found): username and password
        // $connection_string => username:password@protocol+host_specification/database
        if (($at = strrpos($connectionString, '@')) !== false)
        {
            $string = substr($connectionString, 0, $at);
            $connectionString = substr($connectionString, $at + 1);

            if (($position = strpos($string, ':')) !== false)
            {
                $dataSourceName->setUsername(rawurldecode(substr($string, 0, $position)));
                $dataSourceName->setPassword(rawurldecode(substr($string, $position + 1)));
            }
            else
            {
                $dataSourceName->setUsername(rawurldecode($string));
            }
        }

        // Find protocol and host specification

        // $connection_string => protocol(protocol_options)/database
        if (preg_match('|^([^(]+)\((.*?)\)/?(.*?)$|', $connectionString, $match))
        {
            $protocol = $match[1];
            $protocol_options = $match[2] ? $match[2] : false;
            $connectionString = $match[3];

            // $connection_string => protocol+hostspec/database (old format)
        }
        else
        {
            if (strpos($connectionString, '+') !== false)
            {
                list($protocol, $connectionString) = explode('+', $connectionString, 2);
            }

            if (strpos($connectionString, '//') === 0 && strpos($connectionString, '/', 2) !== false &&
                $dataSourceName->getDriver() == 'oci8')
            {
                // oracle's "Easy Connect" syntax: "username/password@[//]host[:port][/service_name]"
                // e.g. "scott/tiger@//mymachine:1521/oracle"
                $protocol_options = $connectionString;
                $connectionString = substr($protocol_options, strrpos($protocol_options, '/') + 1);
            }
            elseif (strpos($connectionString, '/') !== false)
            {
                list($protocol_options, $connectionString) = explode('/', $connectionString, 2);
            }
            else
            {
                $protocol_options = $connectionString;
                $connectionString = null;
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

        $dataSourceName->setHost($protocol_options);
        $dataSourceName->setPort($port);

        // Get database if there is one: $connection_string => database
        if ($connectionString)
        {
            // /database
            if (($position = strpos($connectionString, '?')) === false)
            {
                $dataSourceName->setDatabase($connectionString);
            }
            // /database?param1=value1&param2=value2
            else
            {
                $dataSourceName->setDatabase(substr($connectionString, 0, $position));

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

        return $dataSourceName;
    }

    /**
     * Is the connection string valid for the implementation? Basically checks if the required fields are available.
     *
     * @return boolean
     */
    public function isValid()
    {
        $driver = $this->getDriver();
        if (! isset($driver))
        {
            return false;
        }

        $username = $this->getUsername();
        if (! isset($username))
        {
            return false;
        }

        $host = $this->getHost();
        if (! isset($host))
        {
            return false;
        }

        $database = $this->getDatabase();
        if (! isset($database))
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @return boolean
     * @deprecated Use isValid() now
     */
    public function is_valid()
    {
        return $this->isValid();
    }

    /**
     * Return the actual name of the implementation in a specific storage layer implementation
     *
     * @return string
     * @deprecated Use getImplementedDriver() now
     */
    public function get_implemented_driver()
    {
        return $this->getImplementedDriver();
    }

    /**
     * Return the actual name of the implementation in a specific storage layer implementation
     *
     * @return string
     */
    abstract public function getImplementedDriver();

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
     * @deprecated Use getConnectionString() now
     */
    public function get_connection_string()
    {
        return $this->getConnectionString();
    }

    /**
     *
     * @return string
     */
    public function getConnectionString()
    {
        $string = array();

        $string[] = $this->getDriver(true);
        $string[] = '://';
        $string[] = $this->getUsername();
        $string[] = ':';
        $string[] = $this->getPassword();
        $string[] = '@';
        $string[] = $this->getHost();
        if ($this->getPort())
        {
            $string[] = ':';
            $string[] = $this->getPort();
        }
        $string[] = '/';
        $string[] = $this->getDatabase();

        return implode('', $string);
    }
}
