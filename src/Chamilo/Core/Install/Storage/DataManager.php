<?php
namespace Chamilo\Core\Install\Storage;

use Chamilo\Core\Install\Configuration;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Exception;

/**
 *
 * @package Chamilo\Core\Install\Storage
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataManager
{

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     *
     * @var \Chamilo\Core\Install\Configuration
     */
    private $configuration;

    /**
     *
     * @param \Chamilo\Core\Install\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    protected function initializeConnection()
    {
        $settings = array(
            'driver' => $this->configuration->get_db_driver(),
            'username' => $this->configuration->get_db_username(),
            'password' => $this->configuration->get_db_password(),
            'host' => $this->configuration->get_db_host(),
            'name' => null,
            'charset' => 'utf8');

        $connectionFactory = new ConnectionFactory(new DataSourceName($settings));
        $this->connection = $connectionFactory->getConnection();
    }

    /**
     *
     * @param string $databaseName
     * @return boolean
     */
    protected function storageStructureExists($databaseName)
    {
        return in_array($databaseName, array_map('strtolower', $this->connection->getSchemaManager()->listDatabases()));
    }

    /**
     *
     * @return boolean
     */
    public function initializeStorage()
    {
        try
        {
            $this->initializeConnection();

            $storageStructureName = $this->configuration->get_db_name();
            $overwriteStorageStructure = $this->configuration->get_db_overwrite();
            $storageStructureExists = $this->storageStructureExists($storageStructureName);

            if (! $storageStructureExists)
            {
                $this->connection->getSchemaManager()->createDatabase($storageStructureName);
            }

            elseif ($storageStructureExists && $overwriteStorageStructure)
            {
                $this->connection->getSchemaManager()->dropAndCreateDatabase($storageStructureName);
            }

            return true;
        }
        catch (Exception $exception)
        {
            return false;
        }
    }
}