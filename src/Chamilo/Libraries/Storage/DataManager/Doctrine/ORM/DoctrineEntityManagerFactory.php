<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ChamiloNamingStrategy;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/**
 * Initializes the Doctrine entity manager for use with annotations, caching and the chamilo naming strategy
 * More information can be found at the Doctrine ORM
 *
 * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ORM
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineEntityManagerFactory
{

    /**
     * The mapping driver for the entity manager
     *
     * @var \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
     */
    protected $mappingDriver;

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $doctrineConnection;

    /**
     * The event listeners
     *
     * @var object[]
     */
    protected $eventListeners;

    /**
     * Constructor
     *
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver $mappingDriver
     * @param \Doctrine\DBAL\Connection $doctrineConnection
     */
    public function __construct(MappingDriver $mappingDriver, Connection $doctrineConnection)
    {
        $this->doctrineConnection = $doctrineConnection;
        $this->mappingDriver = $mappingDriver;
        $this->eventListeners = array();
    }

    /**
     * Adds an event listener to the entity manager
     *
     * @param string|array $events
     * @param object $eventListener
     */
    public function addEventListener($events, $eventListener)
    {
        $this->eventListeners[] = array('events' => $events, 'listener' => $eventListener);
    }

    /**
     * Creates and returns the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public function createEntityManager()
    {
        $devMode = false;
        $cache = $devMode ? new ArrayCache() : null;
        $cache = new ArrayCache();

        $configuration = Setup::createConfiguration($devMode, Path::getInstance()->getCachePath(__NAMESPACE__), $cache);

        $configuration->setMetadataDriverImpl($this->mappingDriver);
        $configuration->setNamingStrategy(new ChamiloNamingStrategy());

        $entityManager = EntityManager::create($this->doctrineConnection, $configuration);

        foreach ($this->eventListeners as $eventListener)
        {
            $entityManager->getEventManager()->addEventListener($eventListener['events'], $eventListener['listener']);
        }

        return $entityManager;
    }
}