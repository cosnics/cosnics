<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Storage\Cache\DataManagerCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\DataClassDatabaseFactory;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\Service\DataClassRepository;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataClassRepositoryFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Configuration\Configuration
     */
    private $configuration;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataManagerCache
     */
    private $dataManagerCache;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface
     */
    private $dataClassDatabase;

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     * @param \Chamilo\Libraries\Storage\Cache\DataManagerCache $dataManagerCache
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     */
    public function __construct(Configuration $configuration, DataManagerCache $dataManagerCache,
        DataClassDatabaseInterface $dataClassDatabase)
    {
        $this->configuration = $configuration;
        $this->dataManagerCache = $dataManagerCache;
        $this->dataClassDatabase = $dataClassDatabase;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface
     */
    public function getDataClassDatabase()
    {
        return $this->dataClassDatabase;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     */
    public function setDataClassDatabase($dataClassDatabase)
    {
        $this->dataClassDatabase = $dataClassDatabase;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataManagerCache
     */
    public function getDataManagerCache()
    {
        return $this->dataManagerCache;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataManagerCache $dataManagerCache
     */
    public function setDataManagerCache($dataManagerCache)
    {
        $this->dataManagerCache = $dataManagerCache;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Service\DataClassRepository
     */
    public function getDataClassRepository()
    {
        return new DataClassRepository(
            $this->getConfiguration(),
            $this->getDataManagerCache(),
            $this->getDataClassDatabase());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            $dataClassDatabaseFactory = DataClassDatabaseFactory::getInstance();

            self::$instance = new self(
                Configuration::get_instance(),
                new DataManagerCache(),
                $dataClassDatabaseFactory->getDataClassDatabase());
        }

        return self::$instance;
    }
}