<?php
namespace Chamilo\Core\User\Roles\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\RoleRelation;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Manages roles
 *
 * @package Chamilo\Core\User\Roles\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class UserRoleService implements UserRoleServiceInterface
{

    protected RoleServiceInterface $roleService;

    protected UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(RoleServiceInterface $roleService, UserRoleRepositoryInterface $userRoleRepository)
    {
        $this->roleService = $roleService;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @throws \Exception
     */
    public function addRoleForUser(User $user, string $roleName): void
    {
        $role = $this->getRoleService()->getOrCreateRoleByName($roleName);

        $userRoleRelation = new RoleRelation();

        $userRoleRelation->setRoleId($role->getId());
        $userRoleRelation->setUserId($user->getId());

        if (!$this->getUserRoleRepository()->create($userRoleRelation))
        {
            throw new Exception('User role not created for user ' . $user->get_fullname() . ' with role ' . $roleName);
        }
    }

    /**
     * @param \Chamilo\Core\User\Roles\Storage\DataClass\Role[] $rolesToMatch
     */
    public function doesUserHaveAtLeastOneRole(User $user, array $rolesToMatch = []): bool
    {

        $userRoles = $this->getRolesForUser($user);
        $userRoleIds = [];

        foreach ($userRoles as $userRole)
        {
            $userRoleIds[] = $userRole->getId();
        }

        foreach ($rolesToMatch as $roleToMatch)
        {
            if (in_array($roleToMatch->getId(), $userRoleIds))
            {
                return true;
            }
        }

        return false;
    }

    public function getRoleService(): RoleServiceInterface
    {
        return $this->roleService;
    }

    /**
     * @return \Chamilo\Core\User\Roles\Storage\DataClass\Role[]
     */
    public function getRolesForUser(User $user): array
    {
        $userRoles = $this->userRoleRepository->findRolesForUser($user->getId());

        if ($userRoles->count() == 0)
        {
            $userRoles = [$this->roleService->getOrCreateRoleByName('ROLE_DEFAULT_USER')];
        }

        if ($user->isPlatformAdmin())
        {
            $userRoles[] = $this->roleService->getOrCreateRoleByName('ROLE_ADMINISTRATOR');
        }

        return $userRoles;
    }

    public function getUserRoleRepository(): UserRoleRepositoryInterface
    {
        return $this->userRoleRepository;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    public function getUsersForRole(string $roleName): ArrayCollection
    {
        $role = $this->getRoleService()->getRoleByName($roleName);

        return $this->getUserRoleRepository()->findUsersForRole($role->getId());
    }

    /**
     * @throws \Exception
     */
    public function removeRoleFromUser(User $user, string $roleName): void
    {
        try
        {
            $role = $this->getRoleService()->getRoleByName($roleName);
        }
        catch (Exception)
        {
            return;
        }

        $userRoleRelation =
            $this->getUserRoleRepository()->findUserRoleRelationByRoleAndUser($role->getId(), $user->getId());

        if (!$userRoleRelation)
        {
            return;
        }

        if (!$this->getUserRoleRepository()->delete($userRoleRelation))
        {
            throw new Exception('User role not deleted for user ' . $user->get_fullname() . ' with role ' . $roleName);
        }
    }
}