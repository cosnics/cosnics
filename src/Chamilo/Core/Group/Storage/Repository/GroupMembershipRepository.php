<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException;
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
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
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

        return $this->dataClassRepository->count(
            SubscribedUser::class, new StorageParameters(condition: $condition, joins: $joins)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createGroupMembership(GroupMembership $groupMembership): bool
    {
        return $this->dataClassRepository->create($groupMembership);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembership(GroupMembership $groupMembership): bool
    {
        return $this->dataClassRepository->delete($groupMembership);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByGroupCodeAndUserIdentifier(string $groupCode, string $userId
    ): ?GroupMembership
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER_ID),
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
                    new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
                )
            )
        );

        try {
            return $this->dataClassRepository->retrieve(
                GroupMembership::class, new StorageParameters(condition: $condition, joins: $joins)
            );
        }
        catch (StorageNoResultException) {
            throw new NoSuchGroupMembershipException(
                [Group::PROPERTY_CODE => $groupCode, GroupMembership::PROPERTY_USER_ID => $userId]
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
        string $groupIdentifier, string $userIdentifier
    ): ?GroupMembership
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
        $condition = new AndCondition($conditions);

        try {
            return $this->dataClassRepository->retrieve(
                GroupMembership::class, new StorageParameters(condition: $condition)
            );
        }
        catch (StorageNoResultException) {
            throw new NoSuchGroupMembershipException(
                [
                    GroupMembership::PROPERTY_GROUP_ID => $groupIdentifier,
                    GroupMembership::PROPERTY_USER_ID => $userIdentifier
                ]
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByIdentifier(string $groupMembershipIdentifier): ?GroupMembership
    {
        try {
            return $this->dataClassRepository->retrieveById(GroupMembership::class, $groupMembershipIdentifier);
        }
        catch (StorageNoResultException) {
            throw new NoSuchGroupMembershipException(
                [DataClass::PROPERTY_ID => $groupMembershipIdentifier]
            );
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupMembershipsByGroupIdentifier(string $groupIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupIdentifier)
        );

        return $this->dataClassRepository->retrieves(
            GroupMembership::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupMembershipsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        return $this->dataClassRepository->retrieves(
            GroupMembership::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        $parameters = new StorageParameters(
            condition: $condition, retrieveProperties: new RetrieveProperties(
            [new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER_ID)]
        )
        );

        return $this->dataClassRepository->distinct(GroupMembership::class, $parameters);
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