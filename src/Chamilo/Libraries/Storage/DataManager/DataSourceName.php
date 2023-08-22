<?php
namespace Chamilo\Libraries\Storage\DataManager;

/**
 * Describes a generic database-backed storage layer connection string
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
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

    public function getHost(): string
    {
        return $this->host;
    }

    abstract public function getImplementedDriver(): string;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPort(): ?string
    {
        return $this->port;
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

    public function setCharset(string $charset): static
    {
        $this->charset = $charset;

        return $this;
    }

    public function setDatabase(string $database): static
    {
        $this->database = $database;

        return $this;
    }

    public function setDriver(string $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    public function setHost(string $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function setPort(?string $port): static
    {
        $this->port = $port;

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
}
