<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ChamiloNamingStrategy;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;

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

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected Connection $doctrineConnection;

    /**
     * @var object[]
     */
    protected array $eventListeners;

    protected MappingDriver $mappingDriver;

    public function __construct(
        MappingDriver $mappingDriver, Connection $doctrineConnection, ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        $this->doctrineConnection = $doctrineConnection;
        $this->mappingDriver = $mappingDriver;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->eventListeners = [];
    }

    /**
     * @param string|array $events
     */
    public function addEventListener($events, object $eventListener)
    {
        $this->eventListeners[] = array('events' => $events, 'listener' => $eventListener);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function createEntityManager(): EntityManager
    {
        $cache = new ArrayCache();

        $configuration =
            Setup::createConfiguration(false, $this->configurablePathBuilder->getCachePath(__NAMESPACE__), $cache);

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