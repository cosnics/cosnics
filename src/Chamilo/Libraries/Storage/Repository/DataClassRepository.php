<?php
namespace Chamilo\Libraries\Storage\Repository;

use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;
use Chamilo\Libraries\Storage\Factory\DataClassFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class DataClassRepository
{
    public const ALIAS_MAX_SORT = 'max_sort';

    private DataClassDatabaseInterface $dataClassDatabase;

    private DataClassFactory $dataClassFactory;

    private DataClassRepositoryCache $dataClassRepositoryCache;

    private bool $queryCacheEnabled;

    public function __construct(
        DataClassRepositoryCache $dataClassRepositoryCache, DataClassDatabaseInterface $dataClassDatabase,
        DataClassFactory $dataClassFactory, bool $queryCacheEnabled = true
    )
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->dataClassFactory = $dataClassFactory;
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __count(string $dataClassName, StorageParameters $parameters): int
    {
        $parameters->setRetrieveProperties(
            new RetrieveProperties(
                [
                    new FunctionConditionVariable(
                        FunctionConditionVariable::COUNT,
                        $parameters->getRetrieveProperties()->getFirst(new StaticConditionVariable(1))
                    )
                ]
            )
        );

        return $this->getDataClassDatabase()->count(
            $this->determineDataClassStorageUnitName($dataClassName), $parameters
        );
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __countGrouped(string $dataClassName, StorageParameters $parameters): array
    {
        $retrieveProperties = $parameters->getRetrieveProperties();
        $retrieveProperties->add(
            new FunctionConditionVariable(FunctionConditionVariable::COUNT, new StaticConditionVariable(1))
        );

        return $this->getDataClassDatabase()->countGrouped($dataClassName::getStorageUnitName(), $parameters);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __distinct(string $dataClassName, StorageParameters $parameters): array
    {
        $parameters->setRetrieveProperties(
            new RetrieveProperties([new DistinctConditionVariable($parameters->getRetrieveProperties()->toArray())])
        );

        return $this->getDataClassDatabase()->distinct($dataClassName::getStorageUnitName(), $parameters);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function __record(string $dataClassName, StorageParameters $parameters): array
    {
        $this->applyDataClassPropertiesToParameters($dataClassName, $parameters);

        $parameters->returnSingleResult();

        return $this->getDataClassDatabase()->retrieve(
            $this->determineDataClassStorageUnitName($dataClassName), $parameters
        );
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __records(string $dataClassName, StorageParameters $parameters): ArrayCollection
    {
        $this->applyDataClassPropertiesToParameters($dataClassName, $parameters);

        $records = $this->getDataClassDatabase()->retrieves($dataClassName::getStorageUnitName(), $parameters);

        return new ArrayCollection($records);
    }

    /**
     * @template tInternalRetrieveClass
     *
     * @param class-string<tInternalRetrieveClass> $dataClassName
     *
     * @return tInternalRetrieveClass
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function __retrieve(string $dataClassName, StorageParameters $parameters)
    {
        return $this->getDataClassFactory()->getDataClass(
            $dataClassName, $this->__record($dataClassName, $parameters)
        );
    }

    /**
     * @template tInternalRetrievesClass
     *
     * @param class-string<tInternalRetrievesClass> $dataClassName
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters $parameters
     *
     * @return ArrayCollection<tInternalRetrievesClass>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __retrieves(string $dataClassName, StorageParameters $parameters): ArrayCollection
    {
        $records = $this->__records($dataClassName, $parameters);

        $dataClasses = [];

        foreach ($records as $record) {
            $dataClasses[] = $this->getDataClassFactory()->getDataClass($dataClassName, $record);
        }

        return new ArrayCollection($dataClasses);
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

    protected function buildRetrieveByIdentifierParameters(string $dataClassName, string $identifier): StorageParameters
    {
        return new StorageParameters(
            condition: new EqualityCondition(
                new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID),
                new StaticConditionVariable($identifier)
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function count(string $dataClassName, StorageParameters $parameters = new StorageParameters()): int
    {
        if ($this->isQueryCacheEnabled()) {
            return $this->getDataClassRepositoryCache()->addForCount(
                $dataClassName, $parameters, function () use ($dataClassName, $parameters) {
                return $this->__count($dataClassName, $parameters);
            }
            );
        }
        else {
            return $this->__count($dataClassName, $parameters);
        }
    }

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countGrouped(
        string $dataClassName, StorageParameters $parameters = new StorageParameters()
    ): array
    {
        if ($this->isQueryCacheEnabled()) {
            return $this->getDataClassRepositoryCache()->addForCountGrouped(
                $dataClassName, $parameters, function () use ($dataClassName, $parameters) {
                return $this->__countGrouped($dataClassName, $parameters);
            }
            );
        }
        else {
            return $this->__countGrouped($dataClassName, $parameters);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function create(DataClass $dataClass): bool
    {
        if ($dataClass instanceof UuidDataClassInterface && !$dataClass->isIdentified()) {
            $dataClass->setId(Uuid::v7()->__toString());
        }

        $objectProperties = $dataClass->getDefaultProperties();

        if (!$dataClass instanceof UuidDataClassInterface) {
            unset($objectProperties[DataClass::PROPERTY_ID]);
        }

        $dataClassName = $dataClass::class;

        if ($this->createRecord($dataClassName, $objectProperties)) {
            if (!$dataClass instanceof UuidDataClassInterface) {
                $dataClass->setId(
                    (string) $this->getDataClassDatabase()->getLastInsertedIdentifier($dataClass::getStorageUnitName())
                );
            }

            if ($this->isQueryCacheEnabled()) {
                $this->getDataClassRepositoryCache()->addForRetrieve(
                    $dataClassName, $this->buildRetrieveByIdentifierParameters($dataClassName, $dataClass->getId()),
                    function () use ($dataClass) {
                        return $dataClass;
                    }
                );

                return true;
            }

            return true;
        }

        return false;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createRecord(string $dataClassName, array $record): bool
    {
        return $this->getDataClassDatabase()->create($dataClassName::getStorageUnitName(), $record);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function delete(DataClass $dataClass): bool
    {
        $dataClassName = $dataClass::class;

        $condition = new EqualityCondition(
            new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId())
        );

        return $this->deletes($dataClassName, $condition);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deletes(string $dataClassName, ConditionInterface $condition): bool
    {
        if (!$this->getDataClassDatabase()->delete($dataClassName::getStorageUnitName(), $condition)) {
            return false;
        }

        if ($this->isQueryCacheEnabled()) {
            return $this->getDataClassRepositoryCache()->truncateClass($dataClassName);
        }
        else {
            return true;
        }
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     */
    protected function determineDataClassStorageUnitName(string $dataClassName): string
    {
        return $dataClassName::getStorageUnitName();
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function distinct(string $dataClassName, StorageParameters $parameters = new StorageParameters()): array
    {
        if ($this->isQueryCacheEnabled()) {
            return $this->getDataClassRepositoryCache()->addForDistinct(
                $dataClassName, $parameters, function () use ($dataClassName, $parameters) {
                return $this->__distinct($dataClassName, $parameters);
            }
            );
        }
        else {
            return $this->__distinct($dataClassName, $parameters);
        }
    }

    public function getDataClassDatabase(): DataClassDatabaseInterface
    {
        return $this->dataClassDatabase;
    }

    public function getDataClassFactory(): DataClassFactory
    {
        return $this->dataClassFactory;
    }

    public function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->dataClassRepositoryCache;
    }

    protected function isQueryCacheEnabled(): bool
    {
        return $this->queryCacheEnabled;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function moveDisplayOrders(
        string $dataClassName, string $displayOrderProperty, ?int $start = 1, ?int $end = null,
        ?ConditionInterface $displayOrderCondition = null
    ): bool
    {
        if ($start == $end) {
            return false;
        }

        $displayOrderPropertyVariable = new PropertyConditionVariable($dataClassName, $displayOrderProperty);

        $conditions = [];
        $direction = 0;

        if (is_null($end) || $start < $end) {
            $startOperator = ComparisonCondition::GREATER_THAN;
            $direction = - 1;
        }

        if (!is_null($end)) {
            if ($start < $end) {
                $endOperator = ComparisonCondition::LESS_THAN_OR_EQUAL;
            }
            else {
                $startOperator = ComparisonCondition::LESS_THAN;
                $endOperator = ComparisonCondition::GREATER_THAN_OR_EQUAL;
                $direction = 1;
            }
        }

        $startVariable = new StaticConditionVariable($start);

        $conditions[] = new ComparisonCondition($displayOrderPropertyVariable, $startOperator, $startVariable);

        if (!is_null($end)) {
            $endVariable = new StaticConditionVariable($end);

            $conditions[] = new ComparisonCondition($displayOrderPropertyVariable, $endOperator, $endVariable);
        }

        if ($displayOrderCondition) {
            $conditions[] = $displayOrderCondition;
        }

        $condition = new AndCondition($conditions);

        $updateVariable = new OperationConditionVariable(
            $displayOrderPropertyVariable, OperationConditionVariable::ADDITION, new StaticConditionVariable($direction)
        );

        $properties = new UpdateProperties();

        $properties->add(new UpdateProperty($displayOrderPropertyVariable, $updateVariable));

        return $this->updates($dataClassName, $properties, $condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function record(string $dataClassName, StorageParameters $parameters = new StorageParameters()): ?array
    {
        if ($this->isQueryCacheEnabled()) {
            return $this->getDataClassRepositoryCache()->addForRecord(
                $dataClassName, $parameters, function () use ($dataClassName, $parameters) {
                return $this->__record($dataClassName, $parameters);
            }
            );
        }
        else {
            return $this->__record($dataClassName, $parameters);
        }
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters $parameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function records(
        string $dataClassName, StorageParameters $parameters = new StorageParameters()
    ): ArrayCollection
    {
        if ($this->isQueryCacheEnabled()) {
            $recordIterator = $this->getDataClassRepositoryCache()->addForRecords(
                $dataClassName, $parameters, function () use ($dataClassName, $parameters) {
                return $this->__records($dataClassName, $parameters);
            }
            );
            $recordIterator->first();

            return $recordIterator;
        }
        else {
            return $this->__records($dataClassName, $parameters);
        }
    }

    /**
     * @template retrieveDataClassName
     *
     * @param class-string<retrieveDataClassName> $dataClassName
     *
     * @return retrieveDataClassName
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieve(
        string $dataClassName, StorageParameters $parameters = new StorageParameters()
    )
    {
        //        if ($this->isQueryCacheEnabled())
        //        {
        //            return $this->getDataClassRepositoryCache()->addForRetrieve(
        //                $dataClassName, $parameters, function () use ($dataClassName, $parameters) {
        //                return $this->__retrieve($dataClassName, $parameters);
        //            }
        //            );
        //        }
        //        else
        //        {
        return $this->__retrieve($dataClassName, $parameters);
        //        }
    }

    /**
     * @template retrieveById
     *
     * @param class-string<retrieveById> $dataClassName
     * @param string $identifier
     *
     * @return retrieveById
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveById(string $dataClassName, string $identifier)
    {
        return $this->retrieve(
            $dataClassName, $this->buildRetrieveByIdentifierParameters($dataClassName, $identifier)
        );
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveMaximumValue(string $dataClassName, string $property, ?ConditionInterface $condition = null
    ): int
    {
        $parameters = new StorageParameters(
            condition: $condition, retrieveProperties: new RetrieveProperties(
            [
                new FunctionConditionVariable(
                    FunctionConditionVariable::MAX, new PropertyConditionVariable($dataClassName, $property),
                    self::ALIAS_MAX_SORT
                )
            ]
        )
        );

        $record = $this->getDataClassDatabase()->retrieve($dataClassName::getStorageUnitName(), $parameters);

        return (int) $record[self::ALIAS_MAX_SORT];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveNextValue(string $dataClassName, string $property, ?ConditionInterface $condition = null
    ): int
    {
        return $this->retrieveMaximumValue($dataClassName, $property, $condition) + 1;
    }

    /**
     * @template tRetrieves
     *
     * @param class-string<tRetrieves> $dataClassName
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters $parameters
     *
     * @return ArrayCollection<tRetrieves>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieves(
        string $dataClassName, StorageParameters $parameters = new StorageParameters()
    ): ArrayCollection
    {
        if ($this->isQueryCacheEnabled()) {
            $arrayCollection = $this->getDataClassRepositoryCache()->addForRetrieves(
                $dataClassName, $parameters, function () use ($dataClassName, $parameters) {
                return $this->__retrieves($dataClassName, $parameters);
            }
            );
            $arrayCollection->first();

            return $arrayCollection;
        }
        else {
            return $this->__retrieves($dataClassName, $parameters);
        }
    }

    /**
     * @throws \Throwable
     */
    public function transactional(callable $function): mixed
    {
        return $this->getDataClassDatabase()->transactional($function);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function update(DataClass $dataClass): bool
    {
        $dataClassName = get_class($dataClass);

        $condition = new EqualityCondition(
            new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId())
        );

        $defaultProperties = $dataClass->getDefaultProperties();
        unset($defaultProperties[$dataClassName::PROPERTY_ID]);

        $updatePropertes = new UpdateProperties();

        foreach ($defaultProperties as $propertyName => $propertyValue) {
            $updatePropertes->add(
                new UpdateProperty(
                    new PropertyConditionVariable($dataClassName, $propertyName),
                    new StaticConditionVariable($propertyValue)
                )
            );
        }

        return $this->updates($dataClassName, $updatePropertes, $condition);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updates(string $dataClassName, UpdateProperties $properties, ConditionInterface $condition): bool
    {
        $this->getDataClassDatabase()->update($dataClassName::getStorageUnitName(), $properties, $condition);

        if ($this->isQueryCacheEnabled()) {
            $this->getDataClassRepositoryCache()->truncateClass($dataClassName);
        }

        return true;
    }
}