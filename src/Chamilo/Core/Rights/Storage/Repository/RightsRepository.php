<?php
namespace Chamilo\Core\Rights\Storage\Repository;

use Chamilo\Core\Rights\Domain\RightsLocation;
use Chamilo\Core\Rights\Service\RightsService;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Rights\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
                    $rightsLocationEntityRightClassName, $rightsLocationEntityRightClassName::PROPERTY_RIGHT_ID
                )
            ]
        );

        $join = new Join(
            $rightsLocationClassName, new AndCondition(
                array(
                    new EqualityCondition(
                        new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_ID),
                        new StaticConditionVariable($location->getId())
                    ),

                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $rightsLocationEntityRightClassName,
                            $rightsLocationEntityRightClassName::PROPERTY_LOCATION_ID
                        ), new PropertyConditionVariable(
                            $rightsLocationClassName, $rightsLocationClassName::PROPERTY_ID
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
            new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_INHERIT),
            new StaticConditionVariable(1)
        );

        $parameters = new DataClassDistinctParameters(
            new AndCondition(
                $conditions
            ), new DataClassProperties(
                array(
                    new PropertyConditionVariable(
                        $rightsLocationClassName, $rightsLocationClassName::PROPERTY_IDENTIFIER
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
            new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_INHERIT),
            new StaticConditionVariable(0)
        );
        $nonInheritingConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $rightsLocationEntityRightClassName, $rightsLocationEntityRightClassName::PROPERTY_RIGHT_ID
            ), new StaticConditionVariable($right)
        );

        $parameters = new DataClassDistinctParameters(
            new AndCondition($nonInheritingConditions), new DataClassProperties(
            array(
                new PropertyConditionVariable(
                    $rightsLocationClassName, $rightsLocationClassName::PROPERTY_IDENTIFIER
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
            new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_TREE_TYPE),
            new StaticConditionVariable($treeType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($treeIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_IDENTIFIER),
            new StaticConditionVariable($identifier)
        );

        if ($type != null)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_TYPE),
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
            new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentLocation->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_TYPE),
            new StaticConditionVariable($type)
        );

        $condition = new AndCondition($conditions);

        if (!$parentHasRight)
        {
            $conditions = array();

            $conditions[] = $condition;
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, $rightsLocationEntityRightClassName::PROPERTY_RIGHT_ID
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
                        $rightsLocationClassName, $rightsLocationClassName::PROPERTY_IDENTIFIER
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
                            $rightsLocationEntityRightClassName,
                            $rightsLocationEntityRightClassName::PROPERTY_ENTITY_TYPE
                        ), new StaticConditionVariable($entity->get_entity_type())
                    );
                    $andConditions[] = new InCondition(
                        new PropertyConditionVariable(
                            $rightsLocationEntityRightClassName, $rightsLocationEntityRightClassName::PROPERTY_ENTITY_ID
                        ), $entity->retrieve_entity_item_ids_linked_to_user($userIdentifier)
                    );

                    $orConditions[] = new AndCondition($andConditions);
                }

                // add everyone 'entity'

                $andConditions = array();

                $andConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        $rightsLocationEntityRightClassName, $rightsLocationEntityRightClassName::PROPERTY_ENTITY_TYPE
                    ), new StaticConditionVariable(0)
                );

                $andConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        $rightsLocationEntityRightClassName, $rightsLocationEntityRightClassName::PROPERTY_ENTITY_ID
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
                new PropertyConditionVariable($rightsLocationClassName, $rightsLocationClassName::PROPERTY_ID),
                new PropertyConditionVariable(
                    $rightsLocationEntityRightClassName, $rightsLocationEntityRightClassName::PROPERTY_LOCATION_ID
                )
            )
        );

        return new Joins(array($join));
    }

}