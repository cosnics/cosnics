<?php
namespace Chamilo\Libraries\Rights\Storage\Repository;

use Chamilo\Libraries\Rights\Domain\RightsLocation;
use Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight;
use Chamilo\Libraries\Rights\Service\RightsService;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @package Chamilo\Core\Rights\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsRepository
{
    private DataClassRepository $dataClassRepository;

    private DataClassRepositoryCache $dataClassRepositoryCache;

    /**
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    private array $entitiesConditionCache = [];

    /**
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    private array $entityItemConditionCache = [];

    private NestedSetDataClassRepository $nestedSetDataClassRepository;

    public function __construct(
        NestedSetDataClassRepository $nestedSetDataClassRepository, DataClassRepository $dataClassRepository,
        DataClassRepositoryCache $dataClassRepositoryCache
    )
    {
        $this->nestedSetDataClassRepository = $nestedSetDataClassRepository;
        $this->dataClassRepository = $dataClassRepository;
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
    }

    /**
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param int[] $rights
     * @param int[] $types
     */
    public function countLocationOverviewWithGrantedRights(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, string $userIdentifier,
        array $entities, array $rights = [], array $types = [], ?int $treeType = null, ?string $treeIdentifier = null
    ): int
    {
        $condition = $this->getLocationWithGrantedRightsCondition(
            $rightsLocationClassName, $rightsLocationEntityRightClassName, $rights, $types, $treeType, $treeIdentifier
        );

        $joins = $this->getLocationWithGrantedRightsJoins(
            $rightsLocationClassName, $rightsLocationEntityRightClassName, $userIdentifier, $entities
        );

        $parameters = new DataClassCountParameters(
            $condition, $joins, new RetrieveProperties(
                [
                    new FunctionConditionVariable(
                        FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                            $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                        )
                    )
                ]
            )
        );

        return $this->getDataClassRepository()->count($rightsLocationClassName, $parameters);
    }

    public function createRightsLocation(RightsLocation $location): bool
    {
        return $this->getNestedSetDataClassRepository()->create($location);
    }

    public function createRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight): bool
    {
        $this->getDataClassRepositoryCache()->truncate(get_class($rightsLocationEntityRight));

        return $this->getDataClassRepository()->create($rightsLocationEntityRight);
    }

    public function deleteRightsLocation(string $rightsLocationEntityRightClassName, RightsLocation $rightsLocation
    ): bool
    {
        try
        {
            $deletedLocations = $this->getNestedSetDataClassRepository()->delete($rightsLocation);

            foreach ($deletedLocations as $deletedLocation)
            {
                if (!$this->deleteRightsLocationEntityRightsForLocationAndParameters(
                    $rightsLocationEntityRightClassName, $deletedLocation
                ))
                {
                    return false;
                }
            }

            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }

    public function deleteRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight): bool
    {
        return $this->getDataClassRepository()->delete($rightsLocationEntityRight);
    }

    /**
     * @see DataManager::delete_rights_location_entity_rights()
     */
    public function deleteRightsLocationEntityRightsForLocationAndParameters(
        string $rightsLocationEntityRightClassName, RightsLocation $location, ?string $entityIdentifier = null,
        ?int $entityType = null, ?int $right = null
    ): bool
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($location->getId())
        );

        if ($entityType != null)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable($entityType)
            );
        }

        if ($entityIdentifier != null)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
                ), new StaticConditionVariable($entityIdentifier)
            );
        }

        if ($right != null)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right)
            );
        }

        return $this->getDataClassRepository()->deletes(
            $rightsLocationEntityRightClassName, new AndCondition($conditions)
        );
    }

    /**
     * @return int[]
     */
    public function findGrantedRightsForEntityAndLocation(
        string $rightsLocationEntityRightClassName, string $entityIdentifier, int $entityType, RightsLocation $location
    ): array
    {
        return $this->findGrantedRightsForLocationAndCondition(
            $rightsLocationEntityRightClassName, $location, $this->getEntityItemCondition(
            $rightsLocationEntityRightClassName, $entityIdentifier, $entityType, $location->getId()
        )
        );
    }

    /**
     * @return int[]
     */
    public function findGrantedRightsForLocationAndCondition(
        string $rightsLocationEntityRightClassName, RightsLocation $location, Condition $condition
    ): array
    {
        $rightsLocationClassName = get_class($location);

        $properties = new RetrieveProperties(
            [
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
                )
            ]
        );

        $join = new Join(
            $rightsLocationClassName, new AndCondition(
                [
                    new EqualityCondition(
                        new PropertyConditionVariable($rightsLocationClassName, DataClass::PROPERTY_ID),
                        new StaticConditionVariable($location->getId())
                    ),

                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
                        ), new PropertyConditionVariable(
                            $rightsLocationClassName, DataClass::PROPERTY_ID
                        )
                    )
                ]
            )
        );

        return $this->getDataClassRepository()->distinct(
            $rightsLocationEntityRightClassName, new DataClassDistinctParameters(
                $condition, $properties, new Joins([$join])
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @return int[]
     */
    public function findGrantedRightsForUserIdentifierLocationAndEntities(
        string $rightsLocationEntityRightClassName, string $userIdentifier, RightsLocation $location, array $entities
    ): array
    {
        return $this->findGrantedRightsForLocationAndCondition(
            $rightsLocationEntityRightClassName, $location,
            $this->getEntitiesCondition($rightsLocationEntityRightClassName, $userIdentifier, $entities)
        );
    }

    /**
     * @return string[]
     */
    public function findInheritingIdentifiers(string $rightsLocationClassName, Condition $condition): array
    {
        $conditions = [];

        $conditions[] = $condition;
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_INHERIT),
            new StaticConditionVariable(1)
        );

        $parameters = new DataClassDistinctParameters(
            new AndCondition(
                $conditions
            ), new RetrieveProperties(
                [
                    new PropertyConditionVariable(
                        $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                    )
                ]
            )
        );

        return $this->getDataClassRepository()->distinct($rightsLocationClassName, $parameters);
    }

    /**
     * Returns those ID's from $location_ids which user ($entity_condition) has given right to.
     *
     * @param string $rightsLocationEntityRightClassName
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param int $right
     * @param string[] $locationIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findLocationEntityRightRecordsByGrantedRight(
        string $rightsLocationEntityRightClassName, string $userIdentifier, array $entities, int $right,
        array $locationIdentifiers
    ): ArrayCollection
    {
        $properties = new RetrieveProperties();
        $properties->add(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            )
        );

        $conditions = [];

        $conditions[] = $this->getEntitiesCondition($rightsLocationEntityRightClassName, $userIdentifier, $entities);

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), $locationIdentifiers
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), new StaticConditionVariable($right)
        );

        $parameters = new RecordRetrievesParameters($properties, new AndCondition($conditions));

        return $this->getDataClassRepository()->records($rightsLocationEntityRightClassName, $parameters);
    }

    /**
     * @param string $rightsLocationClassName
     * @param string[] $locationIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findLocationParentIdentifierRecordsForLocationIdentifiers(
        string $rightsLocationClassName, array $locationIdentifiers
    ): ArrayCollection

    {
        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable($rightsLocationClassName, DataClass::PROPERTY_ID),
            array_unique($locationIdentifiers)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_INHERIT),
            new StaticConditionVariable(1)
        );

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, NestedSet::PROPERTY_PARENT_ID),
                new StaticConditionVariable(0)
            )
        );

        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable($rightsLocationClassName, DataClass::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable($rightsLocationClassName, NestedSet::PROPERTY_PARENT_ID));

        $parameters = new RecordRetrievesParameters($properties, new AndCondition($conditions));

        return $this->getDataClassRepository()->records($rightsLocationClassName, $parameters);
    }

    /**
     * @param string $rightsLocationClassName
     * @param string $rightsLocationEntityRightClassName
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param int[] $rights
     * @param int[] $types
     * @param ?int $treeType
     * @param ?string $treeIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findLocationsWithGrantedRights(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, string $userIdentifier,
        array $entities, array $rights = [], array $types = [], ?int $treeType = null, ?string $treeIdentifier = null
    ): ArrayCollection
    {
        $condition = $this->getLocationWithGrantedRightsCondition(
            $rightsLocationClassName, $rightsLocationEntityRightClassName, $rights, $types, $treeType, $treeIdentifier
        );

        $properties = new RetrieveProperties();

        $properties->add(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TYPE)
        );
        $properties->add(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER)
        );

        $joins = $this->getLocationWithGrantedRightsJoins(
            $rightsLocationClassName, $rightsLocationEntityRightClassName, $userIdentifier, $entities
        );

        return $this->getDataClassRepository()->records(
            $rightsLocationClassName, new RecordRetrievesParameters($properties, $condition, null, null, null, $joins)
        );
    }

    /**
     * @return string[]
     */
    public function findNonInheritingIdentifiers(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, Condition $condition,
        Condition $entitiesCondition, int $right
    ): array
    {
        $nonInheritingConditions = [];

        $nonInheritingConditions[] = $condition;
        $nonInheritingConditions[] = $entitiesCondition;
        $nonInheritingConditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_INHERIT),
            new StaticConditionVariable(0)
        );
        $nonInheritingConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), new StaticConditionVariable($right)
        );

        $parameters = new DataClassDistinctParameters(
            new AndCondition($nonInheritingConditions), new RetrieveProperties(
            [
                new PropertyConditionVariable(
                    $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                )
            ]
        ), $this->getRightsLocationEntityRightJoins($rightsLocationClassName, $rightsLocationEntityRightClassName)
        );

        return $this->getDataClassRepository()->distinct($rightsLocationClassName, $parameters);
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param int $right
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see DataManager:: retrieve_target_entities_array()
     */
    public function findRightsEntityRecordsForRightAndLocation(
        string $rightsLocationEntityRightClassName, int $right, RightsLocation $location
    ): ArrayCollection
    {
        $rightsLocationClassName = get_class($location);

        $properties = new RetrieveProperties();
        $properties->add(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            )
        );
        $properties->add(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
            )
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, DataClass::PROPERTY_ID),
            new StaticConditionVariable($location->getId())
        );

        $conditions[] = $condition;

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), new StaticConditionVariable($right)
        );

        $condition = new AndCondition($conditions);

        $parameters = new RecordRetrievesParameters(
            $properties, $condition, null, null, null,
            $this->getRightsLocationEntityRightJoins($rightsLocationClassName, $rightsLocationEntityRightClassName)
        );

        return $this->getDataClassRepository()->records($rightsLocationClassName, $parameters);
    }

    /**
     * @see DataManager::retrieve_rights_location()
     */
    public function findRightsLocationByCondition(string $rightsLocationClassName, Condition $condition
    ): ?RightsLocation
    {
        return $this->getDataClassRepository()->retrieve(
            $rightsLocationClassName, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @see DataManager::retrieve_rights_location_by_id()
     */
    public function findRightsLocationByIdentifier(string $rightsLocationClassName, string $identifier): ?RightsLocation
    {
        return $this->getDataClassRepository()->retrieveById($rightsLocationClassName, $identifier);
    }

    /**
     * @see DataManager::retrieve_rights_location_by_identifier()
     */
    public function findRightsLocationByParameters(
        string $rightsLocationClassName, string $identifier = '0', int $type = RightsService::TYPE_ROOT,
        string $treeIdentifier = '0', int $treeType = RightsService::TREE_TYPE_ROOT
    ): ?RightsLocation
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TREE_TYPE),
            new StaticConditionVariable($treeType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($treeIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER),
            new StaticConditionVariable($identifier)
        );

        if ($type != null)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TYPE),
                new StaticConditionVariable($type)
            );
        }

        return $this->getDataClassRepository()->retrieve(
            $rightsLocationClassName, new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     * @see DataManager::retrieve_rights_location_entity_right_by_id()
     */
    public function findRightsLocationEntityRightByIdentifier(
        string $rightsLocationEntityRightClassName, string $identifier
    ): ?RightsLocationEntityRight
    {
        return $this->getDataClassRepository()->retrieveById(
            $rightsLocationEntityRightClassName, $identifier
        );
    }

    /**
     * @see DataManager::retrieve_rights_location_entity_right()
     */
    public function findRightsLocationEntityRightByParameters(
        string $rightsLocationEntityRightClassName, int $right, string $entityIdentifier, int $entityType,
        string $locationIdentifier
    ): ?RightsLocationEntityRight
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
            ), new StaticConditionVariable($entityIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable($entityType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($locationIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), new StaticConditionVariable($right)
        );

        return $this->getDataClassRepository()->retrieve(
            $rightsLocationEntityRightClassName, new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRights(
        string $rightsLocationEntityRightClassName, ?Condition $condition = null, ?int $offset = null,
        ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            $rightsLocationEntityRightClassName, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param int $right
     * @param string[] $entityIdentifiers
     * @param int $entityType
     * @param string $locationIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightsByParameters(
        string $rightsLocationEntityRightClassName, int $right, array $entityIdentifiers, int $entityType,
        string $locationIdentifier
    ): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
            ), $entityIdentifiers
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable($entityType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($locationIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), new StaticConditionVariable($right)
        );

        return $this->getDataClassRepository()->retrieves(
            $rightsLocationEntityRightClassName, new DataClassRetrievesParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param string $rightsLocationClassName
     * @param string $rightsLocationEntityRightClassName
     * @param int $right
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $parentLocation
     * @param int $type
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param bool $parentHasRight
     *
     * @return string[]
     */
    public function findRightsLocationIdentifiersWithGrantedRight(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, int $right,
        RightsLocation $parentLocation, int $type, string $userIdentifier, array $entities, bool $parentHasRight
    ): array
    {
        $entitiesCondition =
            $this->getEntitiesCondition($rightsLocationEntityRightClassName, $userIdentifier, $entities);

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentLocation->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TYPE),
            new StaticConditionVariable($type)
        );

        $condition = new AndCondition($conditions);

        if (!$parentHasRight)
        {
            $conditions = [];

            $conditions[] = $condition;
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right)
            );

            if ($entitiesCondition instanceof Condition)
            {
                $conditions[] = $entitiesCondition;
            }

            $parameters = new DataClassDistinctParameters(
                new AndCondition($conditions), new RetrieveProperties(
                [
                    new PropertyConditionVariable(
                        $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                    )
                ]
            ), $this->getRightsLocationEntityRightJoins($rightsLocationClassName, $rightsLocationEntityRightClassName)
            );

            return $this->getDataClassRepository()->distinct($rightsLocationClassName, $parameters);
        }
        else
        {
            $inheritingIdentifiers = $this->findInheritingIdentifiers($rightsLocationClassName, $condition);
            $nonInheritingIdentifiers = $this->findNonInheritingIdentifiers(
                $rightsLocationClassName, $rightsLocationEntityRightClassName, $condition, $entitiesCondition, $right
            );

            return array_merge($inheritingIdentifiers, $nonInheritingIdentifiers);
        }
    }

    /**
     * @param string $rightsLocationClassName
     * @param string[] $identifiers
     * @param int $type
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationRecordsByIdentifiersAndType(
        string $rightsLocationClassName, array $identifiers, int $type
    ): ArrayCollection
    {
        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable($rightsLocationClassName, DataClass::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER));

        $conditions[] = new InCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER), $identifiers
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TYPE),
            new StaticConditionVariable($type)
        );
        $condition = new AndCondition($conditions);

        $parameters = new RecordRetrievesParameters($properties, $condition);

        return $this->getDataClassRepository()->records($rightsLocationClassName, $parameters);
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param string $locationIdentifier
     * @param int[] $rights
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationRightsForLocationIdentifierAndRights(
        string $rightsLocationEntityRightClassName, string $locationIdentifier, array $rights
    ): ArrayCollection
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($locationIdentifier)
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), $rights
        );

        $condition = new AndCondition($conditions);

        // order by entity_type to avoid invalid data when looping the rights
        $orderBy = new OrderProperty(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            ), SORT_ASC
        );

        return $this->findRightsLocationEntityRights(
            $rightsLocationEntityRightClassName, $condition, null, null, new OrderBy([$orderBy])
        );
    }

    /**
     * @param string $rightsLocationClassName
     * @param int $treeType
     * @param string $treeIdentifier
     *
     * @return ?\Chamilo\Libraries\Rights\Domain\RightsLocation
     */
    public function findRootLocation(
        string $rightsLocationClassName, int $treeType = RightsService::TREE_TYPE_ROOT, string $treeIdentifier = '0'
    ): ?RightsLocation
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable(0)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TREE_TYPE),
            new StaticConditionVariable($treeType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($treeIdentifier)
        );

        return $this->findRightsLocationByCondition($rightsLocationClassName, new AndCondition($conditions));
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->dataClassRepositoryCache;
    }

    /**
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     */
    private function getEntitiesCondition(
        string $rightsLocationEntityRightClassName, string $userIdentifier, array $entities
    ): ?Condition
    {
        if (!empty($entities))
        {
            $entitiesHash = $this->getEntitiesHash($entities);

            if (is_null($this->entitiesConditionCache[$userIdentifier][$entitiesHash]))
            {
                $orConditions = [];

                foreach ($entities as $entity)
                {
                    $andConditions = [];

                    $andConditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
                        ), new StaticConditionVariable($entity->getEntityType())
                    );
                    $andConditions[] = new InCondition(
                        new PropertyConditionVariable(
                            $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
                        ), $entity->getEntityItemIdentifiersForUserIdentifier($userIdentifier)
                    );

                    $orConditions[] = new AndCondition($andConditions);
                }

                // add everyone 'entity'

                $andConditions = [];

                $andConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
                    ), new StaticConditionVariable(0)
                );

                $andConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
                    ), new StaticConditionVariable(0)
                );

                $orConditions[] = new AndCondition($andConditions);

                $condition = new OrCondition($orConditions);

                $this->entitiesConditionCache[$userIdentifier][$entitiesHash] = $condition;
            }

            return $this->entitiesConditionCache[$userIdentifier][$entitiesHash];
        }

        return null;
    }

    /**
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     */
    private function getEntitiesHash(array $entities): string
    {
        $entitiesIdentifiers = [];

        foreach ($entities as $entityType => $entityProvider)
        {
            $entitiesIdentifiers[] = [$entityType, get_class($entityProvider)];
        }

        return md5(serialize($entitiesIdentifiers));
    }

    public function getEntityItemCondition(
        string $rightsLocationEntityRightClassName, string $entityIdentifier, int $entityType,
        string $locationIdentifier
    ): ?Condition
    {
        $cacheKey =
            md5(serialize([$rightsLocationEntityRightClassName, $locationIdentifier, $entityIdentifier, $entityType,]));

        if (is_null($this->entityItemConditionCache[$cacheKey]))
        {
            $conditions = [];

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable($entityType)
            );

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
                ), new StaticConditionVariable($entityIdentifier)
            );

            $this->entityItemConditionCache[$cacheKey] = new AndCondition($conditions);
        }

        return $this->entityItemConditionCache[$cacheKey];
    }

    /**
     * @param string $rightsLocationClassName
     * @param string $rightsLocationEntityRightClassName
     * @param int[] $rights
     * @param int[] $types
     * @param ?int $treeType
     * @param ?string $treeIdentifier
     *
     * @return ?\Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getLocationWithGrantedRightsCondition(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, array $rights, array $types,
        ?int $treeType = null, ?string $treeIdentifier = null
    ): ?AndCondition
    {
        $conditions = [];

        foreach ($rights as $right_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right_id)
            );
        }

        foreach ($types as $type)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TYPE),
                new StaticConditionVariable($type)
            );
        }

        if (!is_null($treeType))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TREE_TYPE),
                new StaticConditionVariable($treeType)
            );
        }

        if (!is_null($treeIdentifier))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationClassName, RightsLocation::PROPERTY_TREE_IDENTIFIER
                ), new StaticConditionVariable($treeIdentifier)
            );
        }

        if (count($conditions) > 0)
        {
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = null;
        }

        return $condition;
    }

    /**
     * @param string $rightsLocationClassName
     * @param string $rightsLocationEntityRightClassName
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    public function getLocationWithGrantedRightsJoins(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, string $userIdentifier,
        array $entities
    ): Joins
    {
        $joinConditions = [];

        $entitiesCondition =
            $this->getEntitiesCondition($rightsLocationEntityRightClassName, $userIdentifier, $entities);

        if ($entitiesCondition)
        {
            $joinConditions[] = $entitiesCondition;
        }

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new PropertyConditionVariable($rightsLocationClassName, DataClass::PROPERTY_ID)
        );

        return new Joins(
            [new Join($rightsLocationEntityRightClassName, new AndCondition($joinConditions))]
        );
    }

    public function getNestedSetDataClassRepository(): NestedSetDataClassRepository
    {
        return $this->nestedSetDataClassRepository;
    }

    protected function getRightsLocationEntityRightJoins(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName
    ): Joins
    {
        $join = new Join(
            $rightsLocationEntityRightClassName, new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, DataClass::PROPERTY_ID),
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
                )
            )
        );

        return new Joins([$join]);
    }

    public function moveRightsLocation(RightsLocation $location, string $parentLocationIdentifier): bool
    {
        return $this->getNestedSetDataClassRepository()->move($location, (int) $parentLocationIdentifier);
    }

    public function updateRightsLocation(RightsLocation $location): bool
    {
        return $this->getNestedSetDataClassRepository()->update($location);
    }
}