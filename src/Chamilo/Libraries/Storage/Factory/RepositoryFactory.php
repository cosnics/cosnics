<?php
namespace Chamilo\Libraries\Storage\Factory;

use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use function spl_object_id;

/**
 * This factory is used to create default repository objects for entities at runtime.
 */
final class RepositoryFactory implements \Doctrine\ORM\Repository\RepositoryFactory
{
    /**
     * The list of EntityRepository instances.
     *
     * @var ObjectRepository[]
     * @phpstan-var array<string, EntityRepository>
     */
    private array $repositoryList = [];

    public function __construct(protected QueryBuilderConfigurator $queryBuilderConfigurator)
    {
    }

    /**
     * Create a new repository instance for an entity class.
     *
     * @param EntityManagerInterface $entityManager The EntityManager instance.
     * @param string $entityName The name of the entity.
     */
    private function createRepository(
        EntityManagerInterface $entityManager, string $entityName,
    ): EntityRepository
    {
        $metadata = $entityManager->getClassMetadata($entityName);
        $repositoryClassName =
            $metadata->customRepositoryClassName ?: $entityManager->getConfiguration()->getDefaultRepositoryClassName();

        return new $repositoryClassName($entityManager, $metadata, $this->queryBuilderConfigurator);
    }

    public function getRepository(EntityManagerInterface $entityManager, string $entityName): EntityRepository
    {
        $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_id($entityManager);

        return $this->repositoryList[$repositoryHash] ??= $this->createRepository($entityManager, $entityName);
    }
}
