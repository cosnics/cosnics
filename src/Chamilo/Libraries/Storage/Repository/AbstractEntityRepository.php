<?php
namespace Chamilo\Libraries\Storage\Repository;

use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Service\StorageAliasGenerator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractEntityRepository extends EntityRepository
{
    public function __construct(
        EntityManagerInterface $em, ClassMetadata $class, protected StorageAliasGenerator $storageAliasGenerator
    )
    {
        parent::__construct($em, $class);
    }

    protected function buildFromQuery(string $entityType, StorageParameters $parameters): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias($entityType));

        $queryBuilder->from($entityType, $this->getAlias($entityType));
        //        $this->queryBuilderConfigurator->applyParameters(
        //            $queryBuilder, $parameters, $entityType
        //        );

        return $queryBuilder;
    }

    /**
     * @throws \Exception
     */
    public function executeTransaction(callable $callable)
    {
        $this->getEntityManager()->beginTransaction();

        try {
            $result = call_user_func($callable);
            $this->getEntityManager()->commit();

            return $result;
        }
        catch (Exception $exception) {
            $this->getEntityManager()->rollback();
            throw $exception;
        }
    }

    /**
     * @template tEntityType
     * @param class-string<tEntityType> $entityType
     *
     * @return tEntityType|null|object
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     */
    public function findEntity(string $entityType, StorageParameters $parameters): ?object
    {
        $queryBuilder = $this->buildFromQuery($entityType, $parameters);

        return null;
    }

    /**
     * @template tEntityType
     * @param class-string<tEntityType> $entityType
     *
     * @return tEntityType|null|object
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     */
    public function findEntityByIdentifier(string $entityType, Uuid $identifier): ?object
    {
        $entity = $this->find($identifier);

        if (!$entity instanceof $entityType) {
            throw new NoSuchObjectException($entityType, ['identifier' => $identifier]);
        }

        return $entity;
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getAlias(string $dataClassStorageUnitName): string
    {
        return $this->storageAliasGenerator->getTableAlias($dataClassStorageUnitName);
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function refreshEntity(object $entity): void
    {
        $this->getEntityManager()->refresh($entity);
    }

    public function removeEntity(object $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveEntity(object $entity, bool $flush = true): void
    {
        try {
            $this->getEntityManager()->persist($entity);

            if ($flush) {
                $this->flush();
            }
        }
        catch (UniqueConstraintViolationException $exception) {
            throw new EntityAlreadyExistsException(
                $entity::class, $entity, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }
}