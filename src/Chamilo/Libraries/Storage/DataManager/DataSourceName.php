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
    public const DRIVER_IBM = 'ibm';
    public const DRIVER_IBM_DB2 = 'ibm_db2';
    public const DRIVER_MSSQL = 'mssql';
    public const DRIVER_MYSQL = 'mysql';
    public const DRIVER_OCI = 'oci';
    public const DRIVER_OCI8 = 'oci8';
    public const DRIVER_PGSQL = 'pgsql';
    public const DRIVER_SQLITE = 'sqlite';

    private string $charset;

    private string $database;

    private string $driver;

    private string $host;

    private ?string $password;

    private ?string $port;

    /**
     *
     * @var string[]
     */
    private array $settings;

    private string $username;

    /**
     * @param string[] $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->driver = $settings['driver'];
        $this->username = $settings['username'];
        $this->password = $settings['password'] ?? null;
        $this->host = $settings['host'];
        $this->port = $settings['port'] ?? null;
        $this->database = $settings['name'];
        $this->charset = $settings['charset'];
    }

    /**
     * @param string[] $settings
     */
    public static function factory(string $type, array $settings): DataSourceName
    {
        $class = __NAMESPACE__ . '\\' . $type . '\DataSourceName';

        return new $class($settings);
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function setCharset(string $charset)
    {
        $this->charset = $charset;
    }

    public function getConnectionString(): string
    {
        $string = [];

        $string[] = $this->getDriver(true);
        $string[] = '://';
        $string[] = $this->getUsername();

        if ($this->getPassword())
        {
            $string[] = ':';
            $string[] = $this->getPassword();
        }

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

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database)
    {
        $this->database = $database;
    }

    public function getDriver(?bool $implementation = false): string
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

    public function setDriver(string $driver)
    {
        $this->driver = $driver;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    abstract public function getImplementedDriver(): string;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @deprecated Use setPassword() now
     */
    public function set_password(?string $password)
    {
        $this->setPassword($password);
    }

    public function setPassword(?string $password)
    {
        $this->password = $password;
    }

    public function getPort(): ?string
    {
        return $this->port;
    }

    public function setPort(?string $port)
    {
        $this->port = $port;
    }

    /**
     * @return string[]
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @deprecated Use getUsername() now
     */
    public function get_username(): string
    {
        return $this->getUsername();
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @deprecated Use setUsername() now
     */
    public function set_username(string $username)
    {
        $this->setUsername($username);
    }

    public function isValid(): bool
    {
        if (!$this->getDriver())
        {
            return false;
        }

        if (!$this->getUsername())
        {
            return false;
        }

        if (!$this->getHost())
        {
            return false;
        }

        if (!$this->getDatabase())
        {
            return false;
        }

        if (!$this->getCharset())
        {
            return false;
        }

        return true;
    }
}
