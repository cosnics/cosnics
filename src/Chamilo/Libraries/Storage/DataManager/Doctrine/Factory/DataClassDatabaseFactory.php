<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerFactory;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassDatabase;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataClassDatabaseFactory
{

    /**
     * Instance of this class for the singleton pattern.
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\DataClassDatabaseFactory
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
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function __construct(\Doctrine\DBAL\Connection $connection, StorageAliasGenerator $storageAliasGenerator,
        ExceptionLoggerInterface $exceptionLogger)
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
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
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function getExceptionLogger()
    {
        return $this->exceptionLogger;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function setExceptionLogger($exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
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
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase
     */
    public function getDataClassDatabase()
    {
        return new DataClassDatabase(
            $this->getConnection(),
            $this->getStorageAliasGenerator(),
            $this->getExceptionLogger());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\DataClassDatabaseFactory
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            $exceptionLoggerFactory = new ExceptionLoggerFactory(Configuration::get_instance());

            self::$instance = new self(
                ConnectionFactory::getInstance()->getConnection(),
                StorageAliasGenerator::get_instance(),
                $exceptionLoggerFactory->createExceptionLogger());
        }

        return self::$instance;
    }
}
