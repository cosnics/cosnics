<?php
namespace Chamilo\Libraries\Storage\Factory;

use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Cache\Adapter\AdapterInterface;

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
    protected array $eventListeners;

    public function __construct(
        protected MappingDriver $mappingDriver, protected Connection $doctrineConnection,
        protected AdapterInterface $cacheAdapter, protected ConfigurablePathBuilder $configurablePathBuilder,
        protected RepositoryFactory $repositoryFactory
    )
    {
        $this->eventListeners = [];
    }

    public function addEventListener(string|array $events, object $eventListener): void
    {
        $this->eventListeners[] = ['events' => $events, 'listener' => $eventListener];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function createEntityManager(): EntityManager
    {
        Type::addType(UuidType::NAME, UuidType::class);
        Type::addType(UlidType::NAME, UlidType::class);

        $configuration = ORMSetup::createConfiguration(
            proxyDir: $this->configurablePathBuilder->getCachePath(__NAMESPACE__), cache: $this->cacheAdapter
        );

        $configuration->setMetadataDriverImpl($this->mappingDriver);
        $configuration->setRepositoryFactory($this->repositoryFactory);

        $entityManager = new EntityManager($this->doctrineConnection, $configuration);

        foreach ($this->eventListeners as $eventListener) {
            $entityManager->getEventManager()->addEventListener($eventListener['events'], $eventListener['listener']);
        }

        return $entityManager;
    }
}
