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
    const DRIVER_IBM = 'ibm';
    const DRIVER_IBM_DB2 = 'ibm_db2';
    const DRIVER_INTERBASE = 'interbase';
    const DRIVER_MSSQL = 'mssql';
    const DRIVER_MYSQL = 'mysql';
    const DRIVER_OCI = 'oci';
    const DRIVER_OCI8 = 'oci8';
    const DRIVER_PGSQL = 'pgsql';
    const DRIVER_SQLITE = 'sqlite';

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
     * Factory to instantiate the correct type of DataSourceName
     *
     * @param string[] $settings
     *
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
     *
     * @return string
     */
    public function getConnectionString()
    {
        $string = [];

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
     * Set the database we want to connect to
     *
     * @param string $database
     *
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
     * Get the host to be used to make the connection
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
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
     * Set the host to be used to make the connection
     *
     * @param string $host
     *
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
     * Return the actual name of the implementation in a specific storage layer implementation
     *
     * @return string
     */
    abstract public function getImplementedDriver();

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
     * Set the password to be used to make the connection
     *
     * @param string $password
     *
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
     * Get the port to be used to make the connection
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
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
     * Set the port to be used to make the connection
     *
     * @param string $port
     *
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
     *
     * @return string[]
     */
    public function getSettings()
    {
        return $this->settings;
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
     * Set the username to be used to make the connection
     *
     * @param string $username
     *
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
     *
     * @return string
     * @deprecated Use getConnectionString() now
     */
    public function get_connection_string()
    {
        return $this->getConnectionString();
    }

    /**
     * Get the database driver to be used
     *
     * @param boolean $implementation
     *
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
     *
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
     *
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
     * Is the connection string valid for the implementation? Basically checks if the required fields are available.
     *
     * @return boolean
     */
    public function isValid()
    {
        $driver = $this->getDriver();
        if (!isset($driver))
        {
            return false;
        }

        $username = $this->getUsername();
        if (!isset($username))
        {
            return false;
        }

        $host = $this->getHost();
        if (!isset($host))
        {
            return false;
        }

        $database = $this->getDatabase();
        if (!isset($database))
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
}
