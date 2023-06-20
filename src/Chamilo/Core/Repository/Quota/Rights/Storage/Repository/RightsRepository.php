<?php
namespace Chamilo\Core\Repository\Quota\Rights\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight as DomainRightsLocationEntityRight;
use Chamilo\Libraries\Storage\DataClass\DataClass;
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
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsRepository extends \Chamilo\Libraries\Rights\Storage\Repository\RightsRepository
{
    public const PROPERTY_GROUP_ID = 'group_id';

    public function countRightsLocationEntityRightGroups(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(
            RightsLocationEntityRightGroup::class, new DataClassCountParameters($condition)
        );
    }

    public function createRightsLocationEntityRightGroup(
        RightsLocationEntityRightGroup $rightsLocationEntityRightGroup
    ): bool
    {
        return $this->getDataClassRepository()->create($rightsLocationEntityRightGroup);
    }

    public function deleteRightLocationEntityRightGroupsForRightsLocationEntityRight(
        DomainRightsLocationEntityRight $rightsLocationEntityRight
    ): bool
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
            ), new StaticConditionVariable($rightsLocationEntityRight->getId())
        );

        return $this->getDataClassRepository()->deletes(RightsLocationEntityRightGroup::class, $condition);
    }

    public function deleteRightsLocationEntityRightGroup(RightsLocationEntityRightGroup $rightsLocationEntityRightGroup
    ): bool
    {
        return $this->getDataClassRepository()->delete($rightsLocationEntityRightGroup);
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupByIdentifiers(array $identifiers = []): ArrayCollection
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, DataClass::PROPERTY_ID
            ), $identifiers
        );

        return $this->getDataClassRepository()->retrieves(
            RightsLocationEntityRightGroup::class, new DataClassRetrievesParameters($condition)
        );
    }

    public function findRightsLocationEntityRightGroupByParameters(
        string $rightsLocationEntityRightIdentifier, string $groupIdentifier
    ): ?RightsLocationEntityRightGroup
    {
        $conditions = [];

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
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupsForRightsLocationEntityRight(
        RightsLocationEntityRight $rightsLocationEntityRight
    ): ArrayCollection
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
     * @param string[] $rightsLocationEntityRightIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupsForRightsLocationEntityRightIdentifiers(
        array $rightsLocationEntityRightIdentifiers
    ): ArrayCollection
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
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupsWithEntityAndGroupRecords(): ArrayCollection
    {
        $retrieveProperties = new RetrieveProperties();

        $retrieveProperties->add(
            new PropertyConditionVariable(
                RightsLocationEntityRightGroup::class, DataClass::PROPERTY_ID
            )
        );

        $retrieveProperties->add(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, DomainRightsLocationEntityRight::PROPERTY_ENTITY_ID
            )
        );

        $retrieveProperties->add(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, DomainRightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            )
        );

        $retrieveProperties->add(
            new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID, self::PROPERTY_GROUP_ID)
        );
        $retrieveProperties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME));

        $joins = new Joins();

        $joins->add(
            new Join(
                RightsLocationEntityRight::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class, DataClass::PROPERTY_ID
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
                        Group::class, DataClass::PROPERTY_ID
                    ), new PropertyConditionVariable(
                        RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_GROUP_ID
                    )
                )
            )
        );

        return $this->getDataClassRepository()->records(
            RightsLocationEntityRightGroup::class,
            new RecordRetrievesParameters($retrieveProperties, null, null, null, null, $joins)
        );
    }

    /**
     * @param string[] $targetGroupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightsForTargetGroupIdentifiers(array $targetGroupIdentifiers
    ): ArrayCollection
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
                            RightsLocationEntityRight::class, DataClass::PROPERTY_ID
                        )
                    )
                )
            ]
        );

        return $this->getDataClassRepository()->retrieves(
            RightsLocationEntityRight::class, new DataClassRetrievesParameters($condition, null, null, null, $joins)
        );
    }
}