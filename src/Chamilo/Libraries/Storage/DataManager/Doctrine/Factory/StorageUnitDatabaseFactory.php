<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class StorageUnitDatabaseFactory
{

    /**
     * Instance of this class for the singleton pattern.
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\StorageUnitDatabaseFactory
     */
    private static $instance;

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    protected $storageAliasGenerator;

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function __construct(\Doctrine\DBAL\Connection $connection, StorageAliasGenerator $storageAliasGenerator)
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    /**
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    public function getStorageAliasGenerator()
    {
        return $this->storageAliasGenerator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     */
    public function setStorageAliasGenerator($storageAliasGenerator)
    {
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase
     */
    public function getStorageUnitDatabase()
    {
        $configuration = $this->getConfiguration();

        return new StorageUnitDatabase($this->getConnection());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\DataSourceNameFactory
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self(
                ConnectionFactory::getInstance()->getConnection(),
                StorageAliasGenerator::get_instance());
        }

        return self::$instance;
    }
}
