<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupMembership;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Join;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Joins;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipRepository
{
    public function __construct(protected DataClassRepository $dataClassRepository)
    {
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @TODO Implement ORM version
     */
    public function retrieveGroupsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        $joins = new Joins();
        $joins->add(
            new Join(
                GroupMembership::class, new EqualityCondition(
                    new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            Group::class, new StorageParameters(condition: $condition, joins: $joins)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @TODO Implement ORM version
     */
    public function retrieveSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        if ($condition instanceof ConditionInterface) {
            $condition = new AndCondition([$condition, $groupCondition]);
        }
        else {
            $condition = $groupCondition;
        }

        $joins = new Joins(
            [
                new Join(
                    GroupMembership::class, new EqualityCondition(
                        new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER_ID),
                        new PropertyConditionVariable(SubscribedUser::class, DataClass::PROPERTY_ID)
                    )
                )
            ]
        );

        $retrieveProperties = new RetrieveProperties(
            [
                new PropertiesConditionVariable(SubscribedUser::class),
                new PropertyConditionVariable(
                    GroupMembership::class, DataClass::PROPERTY_ID, SubscribedUser::PROPERTY_RELATION_ID
                ),
                new PropertyConditionVariable(
                    GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID, SubscribedUser::PROPERTY_GROUP_ID
                )
            ]
        );

        return $this->dataClassRepository->retrieves(
            SubscribedUser::class, new StorageParameters(
                condition: $condition, joins: $joins, retrieveProperties: $retrieveProperties, orderBy: $orderBy,
                count: $count, offset: $offset
            )
        );
    }
}