<?php
namespace Chamilo\Libraries\Storage\Repository;

use Chamilo\Libraries\Storage\Architecture\Domain\Enum\FunctionTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Doctrine\Common\Collections\ArrayCollection;
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
        EntityManagerInterface $em, ClassMetadata $class, protected QueryBuilderConfigurator $queryBuilderConfigurator
    )
    {
        parent::__construct($em, $class);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters $parameters
     * @param string $dataClassName
     *
     * @return void
     */
    protected function applyDataClassPropertiesToParameters(string $dataClassName, StorageParameters $parameters): void
    {
        if ($parameters->getRetrieveProperties()->isEmpty()) {
            $parameters->getRetrieveProperties()->add(new PropertiesConditionVariable($dataClassName));
        }
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface> $entityType
     *
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function buildFromQuery(string $entityType, StorageParameters $parameters): QueryBuilder
    {
        $alias = $entityType::getAlias();

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->from($entityType, $alias);
        $this->queryBuilderConfigurator->applyParameters(
            $queryBuilder, $parameters, $entityType
        );

        return $queryBuilder;
    }

    /**
     * @template tEntityType
     * @param class-string<tEntityType> $entityType
     *
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function countEntities(string $entityType, StorageParameters $parameters = new StorageParameters()): int
    {
        $parameters->setRetrieveProperties(
            new RetrieveProperties(
                [
                    new FunctionConditionVariable(
                        FunctionTypeEnum::COUNT, new StaticConditionVariable(1)
                    )
                ]
            )
        );

        $query = $this->buildFromQuery($entityType, $parameters)->getQuery();

        return $query->getSingleScalarResult();
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
     * @return \Doctrine\Common\Collections\ArrayCollection<tEntityType|null|object>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findEntities(string $entityType, StorageParameters $parameters = new StorageParameters()
    ): ArrayCollection
    {
        $this->applyDataClassPropertiesToParameters($entityType, $parameters);

        $query = $this->buildFromQuery($entityType, $parameters)->getQuery();

        return new ArrayCollection($query->getResult());
    }

    /**
     * @template tEntityType
     * @param class-string<tEntityType> $entityType
     *
     * @return tEntityType
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findEntity(string $entityType, StorageParameters $parameters = new StorageParameters())
    {
        $this->applyDataClassPropertiesToParameters($entityType, $parameters);
        $parameters->returnSingleResult();

        $query = $this->buildFromQuery($entityType, $parameters)->getQuery();
        $result = $query->getOneOrNullResult();

        if (!$result instanceof DoctrineEntityInterface) {
            throw new NoSuchObjectException(objectType: $entityType, query: $query->getDQL());
        }

        return $result;
    }

    /**
     * @template tEntityType
     * @param class-string<tEntityType> $entityType
     *
     * @return tEntityType
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     */
    public function findEntityByIdentifier(string $entityType, Uuid $identifier)
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
                entityClassname: $entity::class, entity: $entity, message: $exception->getMessage(),
                code: $exception->getCode(), previous: $exception
            );
        }
    }
}