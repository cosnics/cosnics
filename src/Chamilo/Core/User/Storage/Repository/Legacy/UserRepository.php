<?php
namespace Chamilo\Core\User\Storage\Repository\Legacy;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository
{
    public function __construct(protected DataClassRepository $dataClassRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @todo Implement Doctrine ORM
     */
    public function countUsers(?ConditionInterface $condition = null): int
    {
        return $this->dataClassRepository->count(User::class, new StorageParameters(condition: $condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createUser(User $user): void
    {
        $this->dataClassRepository->create($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteUser(User $user): void
    {
        $this->dataClassRepository->delete($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     * @todo Implement Doctrine ORM
     */
    public function retrieveUserByEmail($email): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL), new StaticConditionVariable($email)
        );

        try {
            return $this->dataClassRepository->retrieve(User::class, new StorageParameters(condition: $condition));
        }
        catch (StorageNoResultException) {
            throw new NoSuchUserException([User::PROPERTY_EMAIL => $email]);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function retrieveUserByIdentifier(string $identifier): ?User
    {
        try {
            return $this->dataClassRepository->retrieveById(User::class, $identifier);
        }
        catch (StorageNoResultException) {
            throw new NoSuchUserException([DataClass::PROPERTY_ID => $identifier]);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function retrieveUserByOfficialCode(string $officialCode): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
            new StaticConditionVariable($officialCode)
        );

        try {
            return $this->dataClassRepository->retrieve(User::class, new StorageParameters(condition: $condition));
        }
        catch (StorageNoResultException) {
            throw new NoSuchUserException([User::PROPERTY_OFFICIAL_CODE => $officialCode]);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function retrieveUserBySecurityToken(string $securityToken): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_SECURITY_TOKEN),
            new StaticConditionVariable($securityToken)
        );

        try {
            return $this->dataClassRepository->retrieve(User::class, new StorageParameters(condition: $condition));
        }
        catch (StorageNoResultException) {
            throw new NoSuchUserException([User::PROPERTY_SECURITY_TOKEN => $securityToken]);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function retrieveUserByUsername(string $username): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), new StaticConditionVariable($username)
        );

        try {
            return $this->dataClassRepository->retrieve(User::class, new StorageParameters(condition: $condition));
        }
        catch (StorageNoResultException) {
            throw new NoSuchUserException([User::PROPERTY_USERNAME => $username]);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     * @todo Implement Doctrine ORM
     */
    public function retrieveUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL),
            new StaticConditionVariable($usernameOrEmail)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
            new StaticConditionVariable($usernameOrEmail)
        );

        try {
            return $this->dataClassRepository->retrieve(
                User::class, new StorageParameters(condition: new OrCondition($conditions))
            );
        }
        catch (StorageNoResultException) {
            throw new NoSuchUserException(
                [User::PROPERTY_EMAIL => $usernameOrEmail, User::PROPERTY_USERNAME => $usernameOrEmail]
            );
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\Entity\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @todo Implement Doctrine ORM
     */
    public function retrieveUsers(
        ?ConditionInterface $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $parameters = new StorageParameters(condition: $condition, orderBy: $orderBy, count: $count, offset: $offset);

        return $this->dataClassRepository->retrieves(User::class, $parameters);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\Entity\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @todo Implement Doctrine ORM
     */
    public function retrieveUsersByIdentifiers(array $userIdentifiers, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $condition =
            new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers);

        return $this->dataClassRepository->retrieves(
            User::class, new StorageParameters(
                condition: $condition, orderBy: $orderBy
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUser(User $user): void
    {
        $this->dataClassRepository->update($user);
    }
}