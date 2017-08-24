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
 *
 * More information can be found at the Doctrine ORM
 * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
 *
 * @package application\countries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineEntityManagerFactory
{
    /**
     * The mapping driver for the entity manager
     *
     * @var MappingDriver
     */
    protected $mappingDriver;

    /**
     * @var Connection
     */
    protected $doctrineConnection;

    /**
     * The event listeners
     *
     * @var object
     */
    protected $eventListeners;

    /**
     * Constructor
     *
     * @param MappingDriver $mappingDriver
     * @param Connection $doctrineConnection
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
     * @param $eventListener
     */
    public function addEventListener($events, $eventListener)
    {
        $this->eventListeners[] = array('events' => $events, 'listener' => $eventListener);
    }

    /**
     * Creates and returns the entity manager
     *
     * @return EntityManager
     */
    public function createEntityManager()
    {
        $devMode = false;
        $cache = $devMode ? new ArrayCache() : null;
        $cache = new ArrayCache();

        $configuration = Setup::createConfiguration(
            $devMode, Path::getInstance()->getCachePath(__NAMESPACE__), $cache
        );

        $configuration->setMetadataDriverImpl($this->mappingDriver);
        $configuration->setNamingStrategy(new ChamiloNamingStrategy());

        $entityManager = EntityManager::create($this->doctrineConnection, $configuration);

        foreach($this->eventListeners as $eventListener)
        {
            $entityManager->getEventManager()->addEventListener($eventListener['events'], $eventListener['listener']);
        }

        return $entityManager;
    }
}