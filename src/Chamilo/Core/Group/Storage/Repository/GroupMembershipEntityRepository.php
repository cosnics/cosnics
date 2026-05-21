<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Join;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Joins;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;
use Throwable;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipEntityRepository extends AbstractEntityRepository
{
    public function countSubscribedUsersByGroupIdentifier(
        Uuid $groupIdentifier, ?ConditionInterface $condition = null
    ): int
    {
        return $this->countSubscribedUsersByGroupIdentifiers([$groupIdentifier], $condition);
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     */
    public function countSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null
    ): int
    {
        try {
            $groupCondition = new InCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Group\Storage\DataClass\Group::class, Group::PROPERTY_ID
                ), $groupIdentifiers
            );

            if ($condition instanceof ConditionInterface) {
                $condition = new AndCondition([$condition, $groupCondition]);
            }
            else {
                $condition = $groupCondition;
            }

            return $this->countEntities(
                GroupMembership::class, new StorageParameters(
                    condition: $condition, joins: $this->getGroupMembershipJoins(),
                    retrieveProperties: $this->getGroupMembershipRetrieveProperties()
                )
            );
        }
        catch (Throwable) {
            return 0;
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipByGroupCodeAndUserIdentifier(string $groupCode, Uuid $userIdentifier
    ): GroupMembership
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                GroupMembership::class, GroupMembership::PROPERTY_USER_ID
            ), new StaticConditionVariable($userIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($groupCode)
        );

        $condition = new AndCondition($conditions);

        try {
            return $this->findEntity(
                GroupMembership::class, new StorageParameters(
                    condition: $condition, joins: $this->getGroupMembershipJoins(),
                    retrieveProperties: $this->getGroupMembershipRetrieveProperties()
                )
            );
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupMembershipException(
                query: $exception->query, message: $exception->getMessage(), code: $exception->getCode(),
                previousException: $exception
            );
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipByGroupIdentifierAndUserIdentifier(Uuid $groupIdentifier, Uuid $userIdentifier
    ): GroupMembership
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
            return $this->findEntity(
                GroupMembership::class, new StorageParameters(
                    condition: $condition, joins: $this->getGroupMembershipJoins(),
                    retrieveProperties: $this->getGroupMembershipRetrieveProperties()
                )
            );
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupMembershipException(
                query: $exception->query, message: $exception->getMessage(), code: $exception->getCode(),
                previousException: $exception
            );
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function findGroupMembershipByIdentifier(Uuid $identifier): GroupMembership
    {
        try {
            return $this->findEntityByIdentifier(GroupMembership::class, $identifier);
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupMembershipException(
                $exception->criteria, $exception->query, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @return \Symfony\Component\Uid\Uuid[]
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipUserIdentifiersByGroupIdentifier(Uuid $groupIdentifier): array
    {
        return $this->findGroupMembershipUserIdentifiersByGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     *
     * @return \Symfony\Component\Uid\Uuid[]
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipUserIdentifiersByGroupIdentifiers(array $groupIdentifiers): array
    {
        $condition = new InCondition(
            $this->getIdentityConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP),
            $groupIdentifiers
        );

        $retrieveProperties = new RetrieveProperties(
            [
                new DistinctConditionVariable(
                    [
                        new PropertyConditionVariable(User::class, User::PROPERTY_IDENTIFIER)
                    ]
                )
            ]
        );

        $joins = new Joins(
            [
                new Join(
                    new PropertyConditionVariable(GroupMembership::class, 'user'), User::class
                )
            ]
        );

        $parameters =
            new StorageParameters(condition: $condition, joins: $joins, retrieveProperties: $retrieveProperties);

        $this->applyDataClassPropertiesToParameters(GroupMembership::class, $parameters);

        $query = $this->buildFromQuery(GroupMembership::class, $parameters)->getQuery();

        $result = $query->getResult();

        $identifiers = [];

        foreach ($result as $user) {
            $identifiers[] = $user[User::PROPERTY_IDENTIFIER];
        }
        dump($identifiers);
        exit;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipsByGroupIdentifier(Uuid $groupIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID
            ), new StaticConditionVariable($groupIdentifier)
        );

        return $this->findEntities(
            GroupMembership::class, new StorageParameters(
                condition: $condition, joins: $this->getGroupMembershipJoins(),
                retrieveProperties: $this->getGroupMembershipRetrieveProperties()
            )
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipsByUserIdentifier(Uuid $userIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                GroupMembership::class, GroupMembership::PROPERTY_USER_ID
            ), new StaticConditionVariable($userIdentifier)
        );

        return $this->findEntities(
            GroupMembership::class, new StorageParameters(
                condition: $condition, joins: $this->getGroupMembershipJoins(),
                retrieveProperties: $this->getGroupMembershipRetrieveProperties()
            )
        );
    }

    protected function getGroupMembershipJoins(): Joins
    {
        return new Joins(
            [
                new Join(new PropertyConditionVariable(GroupMembership::class, 'group'), Group::class),
                new Join(new PropertyConditionVariable(GroupMembership::class, 'user'), User::class)
            ]
        );
    }

    protected function getGroupMembershipRetrieveProperties(): RetrieveProperties
    {
        return new RetrieveProperties(
            [
                new PropertiesConditionVariable(GroupMembership::class),
                new PropertiesConditionVariable(Group::class),
                new PropertiesConditionVariable(User::class)
            ]
        );
    }

    public function removeGroupMembership(GroupMembership $groupMembership): void
    {
        $this->getEntityManager()->remove($groupMembership);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveGroupMembership(GroupMembership $groupMembership): void
    {
        $this->saveEntity($groupMembership);
    }
}