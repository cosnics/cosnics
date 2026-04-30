<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countSubscribedUsersByGroupIdentifier(
        string $groupIdentifier, ?ConditionInterface $condition = null
    ): int
    {
        return $this->countSubscribedUsersByGroupIdentifiers([$groupIdentifier], $condition);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null
    ): int
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
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
                    GroupRelUser::class, new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                        new PropertyConditionVariable(SubscribedUser::class, DataClass::PROPERTY_ID)
                    )
                )
            ]
        );

        return $this->dataClassRepository->count(
            SubscribedUser::class, new StorageParameters(condition: $condition, joins: $joins)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createGroupMembership(GroupRelUser $groupUserRelation): bool
    {
        return $this->dataClassRepository->create($groupUserRelation);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembership(GroupRelUser $groupUserRelation): bool
    {
        return $this->dataClassRepository->delete($groupUserRelation);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByGroup(Group $group): bool
    {
        return $this->deleteGroupMembershipsByGroupIdentifiers([$group->getId()]);
    }

    /**
     * @param string[] $groupsIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByGroupIdentifiers(array $groupsIdentifiers): bool
    {
        $condition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupsIdentifiers
        );

        return $this->dataClassRepository->deletes(GroupRelUser::class, $condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveGroupMembershipByGroupCodeAndUserIdentifier(string $groupCode, string $userId
    ): ?GroupRelUser
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

        return $this->dataClassRepository->retrieve(
            GroupRelUser::class, new StorageParameters(condition: $condition, joins: $joins)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
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

        return $this->dataClassRepository->retrieve(
            GroupRelUser::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveGroupMembershipByIdentifier(string $groupRelUserIdentifier): ?GroupRelUser
    {
        return $this->dataClassRepository->retrieveById(GroupRelUser::class, $groupRelUserIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupRelUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupMembershipsByGroupIdentifier(string $groupIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupIdentifier)
        );

        return $this->dataClassRepository->retrieves(
            GroupRelUser::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupRelUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupMembershipsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        return $this->dataClassRepository->retrieves(
            GroupRelUser::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupRelUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        $joins = new Joins();
        $joins->add(
            new Join(
                GroupRelUser::class, new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            Group::class, new StorageParameters(condition: $condition, joins: $joins)
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveSubscribedUserIdentifiersByGroupIdentifier(string $groupIdentifier): array
    {
        return $this->retrieveSubscribedUserIdentifiersByGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveSubscribedUserIdentifiersByGroupIdentifiers(array $groupIdentifiers): array
    {
        $condition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        $parameters = new StorageParameters(
            condition: $condition, retrieveProperties: new RetrieveProperties(
            [new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID)]
        )
        );

        return $this->dataClassRepository->distinct(GroupRelUser::class, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
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

        return $this->dataClassRepository->retrieves(
            SubscribedUser::class, new StorageParameters(
                condition: $condition, joins: $joins, retrieveProperties: $retrieveProperties, orderBy: $orderBy,
                count: $count, offset: $offset
            )
        );
    }
}