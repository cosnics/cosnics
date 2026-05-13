<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository extends AbstractEntityRepository
{
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
                $exception->objectIdentifiers, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByOfficialCode(string $officialCode): User
    {
        $user = $this->findOneBy(['official_code' => $officialCode]);

        if (!$user instanceof User) {
            throw new NoSuchUserException(['official_code' => $officialCode]);
        }

        return $user;
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserBySecurityToken(string $securityToken): User
    {
        $user = $this->findOneBy(['security_token' => $securityToken]);

        if (!$user instanceof User) {
            throw new NoSuchUserException(['security_token' => $securityToken]);
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): User
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(\Chamilo\Core\User\Storage\DataClass\User::class, User::PROPERTY_EMAIL),
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