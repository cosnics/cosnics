<?php
namespace Chamilo\Libraries\Rights\Storage\Repository;

use Chamilo\Libraries\Rights\Domain\RightsLocation;
use Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight;
use Chamilo\Libraries\Rights\Service\RightsService;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
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
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Rights\Storage\Repository
 *
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class RightsRepository
{
    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository
     */
    private $nestedSetDataClassRepository;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    private $dataClassRepositoryCache;

    /**
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    private $entitiesConditionCache;

    /**
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    private $entityItemConditionCache;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository $nestedSetDataClassRepository
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     */
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
     * @param integer $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param integer[] $rights
     * @param integer[] $types
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return integer
     */
    public function countLocationOverviewWithGrantedRights(
        int $userIdentifier, array $entities, array $rights = array(), array $types = array(), $treeType = null,
        $treeIdentifier = null
    )
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();

        $condition = $this->getLocationWithGrantedRightsCondition($rights, $types, $treeType, $treeIdentifier);

        $joins = $this->getLocationWithGrantedRightsJoins($userIdentifier, $entities);

        $parameters = new DataClassCountParameters(
            $condition, $joins, new DataClassProperties(
                array(
                    new FunctionConditionVariable(
                        FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                            $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                        )
                    )
                )
            )
        );

        return $this->getDataClassRepository()->count($rightsLocationClassName, $parameters);
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     *
     * @return boolean
     * @throws \Exception
     */
    public function createRightsLocation(RightsLocation $location)
    {
        return $this->getNestedSetDataClassRepository()->create($location);
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return boolean
     * @throws \Exception
     */
    public function createRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight)
    {
        $this->getDataClassRepositoryCache()->truncate($this->getRightsLocationEntityRightClassName());

        return $this->getDataClassRepository()->create($rightsLocationEntityRight);
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $rightsLocation
     *
     * @return boolean
     * @throws \Exception
     */
    public function deleteRightsLocation(RightsLocation $rightsLocation)
    {
        $deletedLocations = $this->getNestedSetDataClassRepository()->delete($rightsLocation);

        if (!$deletedLocations instanceof DataClassIterator)
        {
            return false;
        }

        foreach ($deletedLocations as $deletedLocation)
        {
            if (!$this->deleteRightsLocationEntityRightsForLocationAndParameters($deletedLocation))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return boolean
     */
    public function deleteRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight)
    {
        return $this->getDataClassRepository()->delete($rightsLocationEntityRight);
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $right
     *
     * @return boolean
     * @see DataManager::delete_rights_location_entity_rights()
     */
    public function deleteRightsLocationEntityRightsForLocationAndParameters(
        RightsLocation $location, int $entityIdentifier = null, int $entityType = null, int $right = null
    )
    {
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $conditions = array();

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
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findGrantedRightsForEntityAndLocation(
        int $entityIdentifier, int $entityType, RightsLocation $location
    )
    {
        return $this->findGrantedRightsForLocationAndCondition(
            $location, $this->getEntityItemCondition($entityIdentifier, $entityType, $location->getId())
        );
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findGrantedRightsForLocationAndCondition(
        RightsLocation $location, Condition $condition
    )
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $properties = new DataClassProperties(
            [
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
                )
            ]
        );

        $join = new Join(
            $rightsLocationClassName, new AndCondition(
                array(
                    new EqualityCondition(
                        new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_ID),
                        new StaticConditionVariable($location->getId())
                    ),

                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
                        ), new PropertyConditionVariable(
                            $rightsLocationClassName, RightsLocation::PROPERTY_ID
                        )
                    )
                )
            )
        );

        return $this->getDataClassRepository()->distinct(
            $rightsLocationEntityRightClassName, new DataClassDistinctParameters(
                $condition, $properties, new Joins(array($join))
            )
        );
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findGrantedRightsForUserIdentifierLocationAndEntities(
        int $userIdentifier, RightsLocation $location, array $entities
    )
    {
        return $this->findGrantedRightsForLocationAndCondition(
            $location, $this->getEntitiesCondition($userIdentifier, $entities)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findInheritingIdentifiers(Condition $condition)
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();

        $conditions = array();

        $conditions[] = $condition;
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_INHERIT),
            new StaticConditionVariable(1)
        );

        $parameters = new DataClassDistinctParameters(
            new AndCondition(
                $conditions
            ), new DataClassProperties(
                array(
                    new PropertyConditionVariable(
                        $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                    )
                )
            )
        );

        return $this->getDataClassRepository()->distinct($rightsLocationClassName, $parameters);
    }

    /**
     * Returns those ID's from $location_ids which user ($entity_condition) has given right to.
     *
     * @param integer $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param integer $right
     * @param integer[] $locationIdentifiers
     *
     * @return string[][] Keys: Those locations ID's from $location_ids which user has given right to. Values: True.
     * @throws \Exception
     */
    public function findLocationEntityRightRecordsByGrantedRight(
        int $userIdentifier, array $entities, int $right, array $locationIdentifiers
    )
    {
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $properties = new DataClassProperties();
        $properties->add(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            )
        );

        $conditions = array();

        $conditions[] = $this->getEntitiesCondition($userIdentifier, $entities);

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
     * @param integer[] $locationIdentifiers
     *
     * @return string[][]
     * @throws \Exception
     */
    public function findLocationParentIdentifierRecordsForLocationIdentifiers(array $locationIdentifiers)

    {
        $rightsLocationClassName = $this->getRightsLocationClassName();

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_ID),
            array_unique($locationIdentifiers)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_INHERIT),
            new StaticConditionVariable(1)
        );

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_PARENT_ID),
                new StaticConditionVariable(0)
            )
        );

        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_PARENT_ID));

        $parameters = new RecordRetrievesParameters($properties, new AndCondition($conditions));

        return $this->getDataClassRepository()->records($rightsLocationClassName, $parameters);
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param integer[] $rights
     * @param integer[] $types
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return string[][]
     * @throws \Exception
     */
    public function findLocationsWithGrantedRights(
        int $userIdentifier, array $entities, array $rights = array(), array $types = array(), $treeType = null,
        $treeIdentifier = null
    )
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();

        $condition = $this->getLocationWithGrantedRightsCondition($rights, $types, $treeType, $treeIdentifier);

        $properties = new DataClassProperties();

        $properties->add(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TYPE)
        );
        $properties->add(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER)
        );

        $joins = $this->getLocationWithGrantedRightsJoins($userIdentifier, $entities);

        return $this->getDataClassRepository()->records(
            $rightsLocationClassName, new RecordRetrievesParameters($properties, $condition, null, $joins)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $entitiesCondition
     * @param integer $right
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findNonInheritingIdentifiers(Condition $condition, Condition $entitiesCondition, int $right)
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $nonInheritingConditions = array();
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
            new AndCondition($nonInheritingConditions), new DataClassProperties(
            array(
                new PropertyConditionVariable(
                    $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                )
            )
        ), $this->getRightsLocationEntityRightJoins()
        );

        return $this->getDataClassRepository()->distinct($rightsLocationClassName, $parameters);
    }

    /**
     * @param integer $right
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     *
     * @return string[][]
     * @throws \Exception
     * @see DataManager:: retrieve_target_entities_array()
     */
    public function findRightsEntityRecordsForRightAndLocation(int $right, RightsLocation $location)
    {
        if (is_null($location) && !is_object($location))
        {
            return array();
        }

        $rightsLocationClassName = $this->getRightsLocationClassName();
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $properties = new DataClassProperties();
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
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_ID),
            new StaticConditionVariable($location->getId())
        );

        if (!is_null($right))
        {
            $conditions[] = $condition;

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right)
            );

            $condition = new AndCondition($conditions);
        }

        $parameters = new RecordRetrievesParameters(
            $properties, $condition, null, null, array(), $this->getRightsLocationEntityRightJoins()
        );

        return $this->getDataClassRepository()->records($rightsLocationClassName, $parameters);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocation
     * @see DataManager::retrieve_rights_location()
     */
    public function findRightsLocationByCondition(Condition $condition)
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();

        return $this->getDataClassRepository()->retrieve(
            $rightsLocationClassName, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param integer $identifier
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocation
     * @see DataManager::retrieve_rights_location_by_id()
     */
    public function findRightsLocationByIdentifier(int $identifier)
    {
        return $this->getDataClassRepository()->retrieveById($this->getRightsLocationClassName(), $identifier);
    }

    /**
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocation
     * @see DataManager::retrieve_rights_location_by_identifier()
     */
    public function findRightsLocationByParameters(
        int $identifier = 0, int $type = RightsService::TYPE_ROOT, int $treeIdentifier = 0,
        int $treeType = RightsService::TREE_TYPE_ROOT
    )
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();

        $conditions = array();
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
     * @param integer $identifier
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight
     * @see DataManager::retrieve_rights_location_entity_right_by_id()
     */
    public function findRightsLocationEntityRightByIdentifier(int $identifier)
    {
        return $this->getDataClassRepository()->retrieveById(
            $this->getRightsLocationEntityRightClassName(), $identifier
        );
    }

    /**
     * @param integer $right
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $locationIdentifier
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight
     * @see DataManager::retrieve_rights_location_entity_right()
     */
    public function findRightsLocationEntityRightByParameters(
        int $right, int $entityIdentifier, int $entityType, int $locationIdentifier
    )
    {
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $conditions = array();

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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight[]
     */
    public function findRightsLocationEntityRights(
        Condition $condition = null, int $offset = null, int $count = null, array $orderBy = null
    )
    {
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        return $this->getDataClassRepository()->retrieves(
            $rightsLocationEntityRightClassName, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }

    /**
     * @param integer $right
     * @param integer[] $entityIdentifiers
     * @param integer $entityType
     * @param integer $locationIdentifier
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight[]
     */
    public function findRightsLocationEntityRightsByParameters(
        int $right, array $entityIdentifiers, int $entityType, int $locationIdentifier
    )
    {
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $conditions = array();

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
     * @param integer $right
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $parentLocation
     * @param integer $type
     * @param integer $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param boolean $parentHasRight
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findRightsLocationIdentifiersWithGrantedRight(
        int $right, RightsLocation $parentLocation, int $type, int $userIdentifier, array $entities,
        bool $parentHasRight
    )
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $entitiesCondition = $this->getEntitiesCondition($userIdentifier, $entities);

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentLocation->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_TYPE),
            new StaticConditionVariable($type)
        );

        $condition = new AndCondition($conditions);

        if (!$parentHasRight)
        {
            $conditions = array();

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
                new AndCondition($conditions), new DataClassProperties(
                array(
                    new PropertyConditionVariable(
                        $rightsLocationClassName, RightsLocation::PROPERTY_IDENTIFIER
                    )
                )
            ), $this->getRightsLocationEntityRightJoins()
            );

            return $this->getDataClassRepository()->distinct($rightsLocationClassName, $parameters);
        }
        else
        {
            $inheritingIdentifiers = $this->findInheritingIdentifiers($condition);
            $nonInheritingIdentifiers = $this->findNonInheritingIdentifiers($condition, $entitiesCondition, $right);

            return array_merge($inheritingIdentifiers, $nonInheritingIdentifiers);
        }
    }

    /**
     * @param integer[] $identifiers
     * @param integer $type
     *
     * @return string[][]
     * @throws \Exception
     */
    public function findRightsLocationRecordsByIdentifiersAndType(array $identifiers, int $type)
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_ID));
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
     * @param integer $locationIdentifier
     * @param integer[] $rights
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight[]
     */
    public function findRightsLocationRightsForLocationIdentifierAndRights(int $locationIdentifier, array $rights)
    {
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        if (!is_array($rights))
        {
            $rights = array($rights);
        }

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
        $orderBy = new OrderBy(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            ), SORT_ASC
        );

        return $this->findRightsLocationEntityRights($condition, null, null, array($orderBy));
    }

    /**
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocation
     */
    public function findRootLocation($treeType = RightsService::TREE_TYPE_ROOT, $treeIdentifier = 0)
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_PARENT_ID),
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

        return $this->findRightsLocationByCondition(new AndCondition($conditions));
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository): void
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    public function getDataClassRepositoryCache()
    {
        return $this->dataClassRepositoryCache;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     */
    public function setDataClassRepositoryCache(DataClassRepositoryCache $dataClassRepositoryCache)
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private function getEntitiesCondition(int $userIdentifier, array $entities)
    {
        if (!empty($entities))
        {
            $entitiesHash = $this->getEntitiesHash($entities);

            if (is_null($this->entitiesConditionCache[$userIdentifier][$entitiesHash]))
            {
                $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

                $orConditions = array();

                foreach ($entities as $entity)
                {
                    $andConditions = array();

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

                $andConditions = array();

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
     *
     * @return string
     */
    private function getEntitiesHash(array $entities)
    {
        $entitiesIdentifiers = array();

        foreach ($entities as $entityType => $entityProvider)
        {
            $entitiesIdentifiers[] = array($entityType, get_class($entityProvider));
        }

        return md5(serialize($entitiesIdentifiers));
    }

    /**
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $locationIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function getEntityItemCondition(int $entityIdentifier, int $entityType, int $locationIdentifier)
    {
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $cacheKey =
            md5(serialize([$rightsLocationEntityRightClassName, $locationIdentifier, $entityIdentifier, $entityType,]));

        if (is_null($this->entityItemConditionCache[$cacheKey]))
        {
            $conditions = array();

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
     * @param array $rights
     * @param array $types
     * @param $treeType
     * @param $treeIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getLocationWithGrantedRightsCondition(
        array $rights, array $types, $treeType, $treeIdentifier
    )
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $conditions = array();

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
     * @param int $userIdentifier
     * @param array $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    public function getLocationWithGrantedRightsJoins(int $userIdentifier, array $entities)
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $joinConditions = array();

        $entitiesCondition = $this->getEntitiesCondition($userIdentifier, $entities);

        if ($entitiesCondition)
        {
            $joinConditions[] = $entitiesCondition;
        }

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_ID)
        );

        return new Joins(
            array(new Join($rightsLocationEntityRightClassName, new AndCondition($joinConditions)))
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository
     */
    public function getNestedSetDataClassRepository(): NestedSetDataClassRepository
    {
        return $this->nestedSetDataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository $nestedSetDataClassRepository
     */
    public function setNestedSetDataClassRepository(NestedSetDataClassRepository $nestedSetDataClassRepository): void
    {
        $this->nestedSetDataClassRepository = $nestedSetDataClassRepository;
    }

    /**
     * @return string
     */
    abstract public function getRightsLocationClassName(): string;

    /**
     * @return string
     */
    abstract public function getRightsLocationEntityRightClassName(): string;

    /**
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getRightsLocationEntityRightJoins()
    {
        $rightsLocationClassName = $this->getRightsLocationClassName();
        $rightsLocationEntityRightClassName = $this->getRightsLocationEntityRightClassName();

        $join = new Join(
            $rightsLocationEntityRightClassName, new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, RightsLocation::PROPERTY_ID),
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_LOCATION_ID
                )
            )
        );

        return new Joins(array($join));
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     * @param integer $parentLocationIdentifier
     *
     * @return boolean
     * @throws \Exception
     */
    public function moveRightsLocation(RightsLocation $location, $parentLocationIdentifier)
    {
        return $this->getNestedSetDataClassRepository()->move($location, $parentLocationIdentifier);
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     *
     * @return boolean
     * @throws \Exception
     */
    public function updateRightsLocation(RightsLocation $location)
    {
        return $this->getNestedSetDataClassRepository()->update($location);
    }
}