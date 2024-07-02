<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\DataClassParameters;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function countSubscribedUsersForGroupIdentifier(string $groupIdentifier, ?Condition $condition = null): int
    {
        return $this->countSubscribedUsersForGroupIdentifiers([$groupIdentifier], $condition);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function countSubscribedUsersForGroupIdentifiers(array $groupIdentifiers, ?Condition $condition = null): int
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition([$condition, $groupCondition]);
        }
        else
        {
            $condition = $groupCondition;
        }

        $joins = new Joins(
            [
                new Join(
                    GroupRelUser::class, new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                        new PropertyConditionVariable(SubscribedUser::class, DataClass::PROPERTY_ID)
                    )
                )
            ]
        );

        return $this->getDataClassRepository()->count(
            SubscribedUser::class, new DataClassParameters(condition: $condition, joins: $joins)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageLastInsertedIdentifierException
     */
    public function createGroupUserRelation(GroupRelUser $groupUserRelation): bool
    {
        return $this->dataClassRepository->create($groupUserRelation);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function deleteGroupUserRelation(GroupRelUser $groupUserRelation): bool
    {
        return $this->dataClassRepository->delete($groupUserRelation);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function emptyGroup(Group $group): bool
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->getId())
        );

        return $this->getDataClassRepository()->deletes(GroupRelUser::class, $condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageNoResultException
     */
    public function findGroupRelUserByGroupAndUserId(string $groupId, string $userId): ?GroupRelUser
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupId)
        );

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            GroupRelUser::class, new DataClassParameters(condition: $condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageNoResultException
     */
    public function findGroupRelUserByGroupCodeAndUserId(string $groupCode, string $userId): ?GroupRelUser
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($groupCode)
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                Group::class, new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
                )
            )
        );

        return $this->getDataClassRepository()->retrieve(
            GroupRelUser::class, new DataClassParameters(condition: $condition, joins: $joins)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageNoResultException
     */
    public function findGroupRelUserByIdentifier(string $groupRelUserIdentifier): ?GroupRelUser
    {
        return $this->getDataClassRepository()->retrieveById(GroupRelUser::class, $groupRelUserIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageNoResultException
     */
    public function findGroupUserRelationByGroupIdentifierAndUserIdentifier(
        string $groupIdentifier, string $userIdentifier
    ): ?GroupRelUser
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            GroupRelUser::class, new DataClassParameters(condition: $condition)
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function findSubscribedUserIdentifiersForGroupIdentifier(string $groupIdentifier): array
    {
        return $this->findSubscribedUserIdentifiersForGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function findSubscribedUserIdentifiersForGroupIdentifiers(array $groupIdentifiers): array
    {
        $condition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        $parameters = new DataClassParameters(
            condition: $condition, retrieveProperties: new RetrieveProperties(
            [new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID)]
        )
        );

        return $this->getDataClassRepository()->distinct(GroupRelUser::class, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function findSubscribedUsersForGroupIdentifiers(
        array $groupIdentifiers, ?Condition $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition([$condition, $groupCondition]);
        }
        else
        {
            $condition = $groupCondition;
        }

        $joins = new Joins(
            [
                new Join(
                    GroupRelUser::class, new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                        new PropertyConditionVariable(SubscribedUser::class, DataClass::PROPERTY_ID)
                    )
                )
            ]
        );

        $retrieveProperties = new RetrieveProperties(
            [
                new PropertiesConditionVariable(SubscribedUser::class),
                new PropertyConditionVariable(
                    GroupRelUser::class, DataClass::PROPERTY_ID, SubscribedUser::PROPERTY_RELATION_ID
                ),
                new PropertyConditionVariable(
                    GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID, SubscribedUser::PROPERTY_GROUP_ID
                )
            ]
        );

        return $this->getDataClassRepository()->retrieves(
            SubscribedUser::class, new DataClassParameters(
                condition: $condition, joins: $joins, retrieveProperties: $retrieveProperties, orderBy: $orderBy,
                count: $count, offset: $offset
            )
        );
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param string $groupIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupRelUser>
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function getGroupUserRelationsByGroupIdentifier(string $groupIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupIdentifier)
        );

        return $this->getDataClassRepository()->retrieves(
            GroupRelUser::class, new DataClassParameters(condition: $condition)
        );
    }

    /**
     * @param string[] $groupsIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function unsubscribeUsersFromGroupIdentifiers(array $groupsIdentifiers): bool
    {
        $condition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupsIdentifiers
        );

        return $this->getDataClassRepository()->deletes(GroupRelUser::class, $condition);
    }
}