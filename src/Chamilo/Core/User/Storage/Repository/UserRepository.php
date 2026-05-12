<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository extends AbstractEntityRepository
{
    public function deleteUser(User $user): void
    {
        $this->getEntityManager()->remove($user);
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
    public function findUserByUsername(string $securityToken): User
    {
        $user = $this->findOneBy(['username' => $securityToken]);

        if (!$user instanceof User) {
            throw new NoSuchUserException(['username' => $securityToken]);
        }

        return $user;
    }

//    public function findUserByUsernameOrEmail(string $usernameOrEmail): User
//    {
//        $queryBuilder = $this->createQueryBuilder('u');
//    }

    public function saveUser(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}