<?php
namespace Chamilo\Core\Rights\Storage\Repository;

use Chamilo\Core\Rights\Domain\RightsLocation;
use Chamilo\Core\Rights\Domain\RightsLocationEntityRight;
use Chamilo\Core\Rights\Service\RightsService;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Rights\Storage\Repository
 *
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsRepository
{
    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @var string
     */
    private $rightsLocationClassName;

    /**
     * @var string
     */
    private $rightsLocationEntityRightClassName;

    /**
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    private $entitiesConditionCache;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param string $rightsLocationClassName
     * @param string $rightsLocationEntityRightClassName
     */
    public function __construct(
        DataClassRepository $dataClassRepository, string $rightsLocationClassName,
        string $rightsLocationEntityRightClassName
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->rightsLocationClassName = $rightsLocationClassName;
        $this->rightsLocationEntityRightClassName = $rightsLocationEntityRightClassName;
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return integer[]
     */
    public function findGrantedRightsForUserIdentifierLocationAndEntities(
        int $userIdentifier, RightsLocation $location, array $entities
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
                $this->getEntitiesCondition($entities), $properties, new Joins(array($join))
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer[]
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $entitiesCondition
     * @param integer $right
     *
     * @return integer[]
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
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     */
    public function findRightsLocationByIdentifier(int $identifier)
    {
        return $this->getDataClassRepository()->retrieveById($this->getRightsLocationClassName(), $identifier);
    }

    /**
     * @param integer[] $identifiers
     * @param integer $type
     *
     * @return string[]
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
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
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
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $parentLocation
     * @param integer $type
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param boolean $parentHasRight
     *
     * @return integer[]
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

            return $this->getDataClassRepository()->distinct($rightsLocationEntityRightClassName, $parameters);
        }
        else
        {
            $inheritingIdentifiers = $this->findInheritingIdentifiers($condition);
            $nonInheritingIdentifiers = $this->findNonInheritingIdentifiers($condition, $entitiesCondition, $right);

            return array_merge($inheritingIdentifiers, $nonInheritingIdentifiers);
        }
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
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private function getEntitiesCondition(int $userIdentifier, array $entities)
    {
        if (!empty($entities))
        {
            $entitiesHash = md5(serialize($entities));

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
                        ), new StaticConditionVariable($entity->get_entity_type())
                    );
                    $andConditions[] = new InCondition(
                        new PropertyConditionVariable(
                            $rightsLocationEntityRightClassName, RightsLocationEntityRight::PROPERTY_ENTITY_ID
                        ), $entity->retrieve_entity_item_ids_linked_to_user($userIdentifier)
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
    }

    /**
     * @param array $rights
     * @param array $types
     * @param $treeType
     * @param $treeIdentifier
     * @param $rightsLocationEntityRightClassName
     * @param $rightsLocationClassName
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition|null
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
            array(new Join($rightsLocationEntityRightClassName, new AndCondition(array($joinConditions))))
        );
    }

    /**
     * @return string
     */
    public function getRightsLocationClassName(): string
    {
        return $this->rightsLocationClassName;
    }

    /**
     * @param string $rightsLocationClassName
     */
    public function setRightsLocationClassName(string $rightsLocationClassName): void
    {
        $this->rightsLocationClassName = $rightsLocationClassName;
    }

    /**
     * @return string
     */
    public function getRightsLocationEntityRightClassName(): string
    {
        return $this->rightsLocationEntityRightClassName;
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     */
    public function setRightsLocationEntityRightClassName(string $rightsLocationEntityRightClassName): void
    {
        $this->rightsLocationEntityRightClassName = $rightsLocationEntityRightClassName;
    }

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
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer[] $rights
     * @param integer[] $types
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation[]
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
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
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
     * @param integer[] $locationIdentifiers
     *
     * @return string[]
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
     * Returns those ID's from $location_ids which user ($entity_condition) has given right to.
     *
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $right
     * @param integer[] $locationIdentifiers
     *
     * @return string[] Keys: Those locations ID's from $location_ids which user has given right to. Values: True.
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
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     *
     * @return string[]
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
}