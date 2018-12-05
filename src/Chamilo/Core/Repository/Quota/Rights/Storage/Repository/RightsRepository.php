<?php
namespace Chamilo\Core\Repository\Quota\Rights\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsRepository extends \Chamilo\Libraries\Rights\Storage\Repository\RightsRepository
{
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countRightsLocationEntityRightGroups(Condition $condition = null)
    {
        return $this->getDataClassRepository()->count(
            RightsLocationEntityRightGroup::class, new DataClassCountParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup $rightsLocationEntityRightGroup
     *
     * @return boolean
     * @throws \Exception
     */
    public function createRightsLocationEntityRightGroup(
        RightsLocationEntityRightGroup $rightsLocationEntityRightGroup
    )
    {
        return $this->getDataClassRepository()->create($rightsLocationEntityRightGroup);
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup $rightsLocationEntityRightGroup
     *
     * @return boolean
     */
    public function deleteRightsLocationEntityRightGroup(RightsLocationEntityRightGroup $rightsLocationEntityRightGroup)
    {
        return $this->getDataClassRepository()->delete($rightsLocationEntityRightGroup);
    }

    /**
     * @param integer[] $identifiers
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     */
    public function findRightsLocationEntityRightGroupByIdentifiers(array $identifiers = array())
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_ID
            ), $identifiers
        );

        return $this->getDataClassRepository()->retrieves(
            RightsLocationEntityRightGroup::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param integer $rightsLocationEntityRightIdentifier
     * @param integer $groupIdentifier
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup
     */
    public function findRightsLocationEntityRightGroupByParameters(
        int $rightsLocationEntityRightIdentifier, int $groupIdentifier
    )
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
            ), new StaticConditionVariable($rightsLocationEntityRightIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_GROUP_ID
            ), new StaticConditionVariable($groupIdentifier)
        );

        return $this->getDataClassRepository()->retrieve(
            RightsLocationEntityRightGroup::class, new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     * @return string[][]
     * @throws \Exception
     */
    public function findRightsLocationEntityRightGroupsWithEntityAndGroupRecords()
    {
        $dataClassProperties = new DataClassProperties();

        $dataClassProperties->add(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_ID
            )
        );

        $dataClassProperties->add(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_ID
            )
        );

        $dataClassProperties->add(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            )
        );

        $dataClassProperties->add(
            new FixedPropertyConditionVariable(Group::class, Group::PROPERTY_ID, self::PROPERTY_GROUP_ID)
        );
        $dataClassProperties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME));

        $joins = new Joins();

        $joins->add(
            new Join(
                RightsLocationEntityRight::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ID
                    ), new PropertyConditionVariable(
                        RightsLocationEntityRightGroup::class,
                        RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
                    )
                )
            )
        );

        $joins->add(
            new Join(
                Group::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        Group::class, Group::PROPERTY_ID
                    ), new PropertyConditionVariable(
                        RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_GROUP_ID
                    )
                )
            )
        );

        return $this->getDataClassRepository()->records(
            RightsLocationEntityRightGroup::class,
            new RecordRetrievesParameters($dataClassProperties, null, null, null, null, $joins)
        );
    }

    /**
     * @return string
     */
    public function getRightsLocationClassName(): string
    {
        return RightsLocation::class;
    }

    /**
     * @return string
     */
    public function getRightsLocationEntityRightClassName(): string
    {
        return RightsLocationEntityRight::class;
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     */
    public function findRightsLocationEntityRightGroupsForRightsLocationEntityRight(
        RightsLocationEntityRight $rightsLocationEntityRight
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
            ), new StaticConditionVariable($rightsLocationEntityRight->getId())
        );

        return $this->getDataClassRepository()->retrieves(
            RightsLocationEntityRightGroup::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param integer[] $rightsLocationEntityRightIdentifiers
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     */
    public function findRightsLocationEntityRightGroupsForRightsLocationEntityRightIdentifiers(
        array $rightsLocationEntityRightIdentifiers
    )
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
            ), $rightsLocationEntityRightIdentifiers
        );

        return $this->getDataClassRepository()->retrieves(
            RightsLocationEntityRightGroup::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param integer[] $targetGroupIdentifiers
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight[]
     */
    public function findRightsLocationEntityRightsForTargetGroupIdentifiers(array $targetGroupIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_GROUP_ID
            ), $targetGroupIdentifiers
        );

        $joins = new Joins(
            [
                new Join(
                    RightsLocationEntityRightGroup::class, new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRightGroup::class,
                            RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
                        ), new PropertyConditionVariable(
                            RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ID
                        )
                    )
                )
            ]
        );

        return $this->getDataClassRepository()->retrieves(
            RightsLocationEntityRight::class, new DataClassRetrievesParameters($condition, null, null, array(), $joins)
        );
    }
}