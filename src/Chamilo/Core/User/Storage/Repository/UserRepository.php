<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository extends AbstractEntityRepository
{
    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function countUsers(?ConditionInterface $condition = null): int
    {
        return $this->countEntities(User::class, new StorageParameters(condition: $condition));
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByEmail(string $securityToken): User
    {
        $user = $this->findOneBy(['email' => $securityToken]);

        if (!$user instanceof User) {
            throw new NoSuchUserException(['email' => $securityToken]);
        }

        return $user;
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByIdentifier(Uuid $identifier): User
    {
        try {
            return $this->findEntityByIdentifier(User::class, $identifier);
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchUserException(
                $exception->criteria, $exception->query, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByOfficialCode(string $officialCode): User
    {
        $user = $this->findOneBy(['officialCode' => $officialCode]);

        if (!$user instanceof User) {
            throw new NoSuchUserException(['officialCode' => $officialCode]);
        }

        return $user;
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserBySecurityToken(string $securityToken): User
    {
        $user = $this->findOneBy(['securityToken' => $securityToken]);

        if (!$user instanceof User) {
            throw new NoSuchUserException(['securityToken' => $securityToken]);
        }

        return $user;
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByUsername(string $username): User
    {
        $user = $this->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new NoSuchUserException(['username' => $username]);
        }

        return $user;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): User
    {
        try {
            $conditions = [];

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL),
                new StaticConditionVariable($usernameOrEmail)
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                new StaticConditionVariable($usernameOrEmail)
            );

            return $this->findEntity(
                User::class, new StorageParameters(condition: new OrCondition($conditions))
            );
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchUserException(
                $exception->criteria, $exception->query, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\Entity\User>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findUsers(
        ?ConditionInterface $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $parameters = new StorageParameters(condition: $condition, orderBy: $orderBy, count: $count, offset: $offset);

        return $this->findEntities(User::class, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findUsersByIdentifiers(array $userIdentifiers, OrderBy $orderBy = new OrderBy()): ArrayCollection
    {
        $condition = new InCondition(
            new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers
        );

        return $this->findEntities(
            User::class, new StorageParameters(
                condition: $condition, orderBy: $orderBy
            )
        );
    }

    public function removeUser(User $user): void
    {
        $this->getEntityManager()->remove($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveUser(User $user): void
    {
        $this->saveEntity($user);
    }
}