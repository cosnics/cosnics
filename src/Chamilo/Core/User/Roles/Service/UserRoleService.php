<?php

namespace Chamilo\Core\User\Roles\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\DataClass\RoleRelation;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Manages roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRoleService implements UserRoleServiceInterface
{
    /**
     * @var RoleServiceInterface
     */
    protected $roleService;

    /**
     * @var UserRoleRepositoryInterface
     */
    protected $userRoleRepository;

    /**
     * UserRoleService constructor.
     *
     * @param RoleServiceInterface $roleService
     * @param UserRoleRepositoryInterface $userRoleRepository
     */
    public function __construct(RoleServiceInterface $roleService, UserRoleRepositoryInterface $userRoleRepository)
    {
        $this->roleService = $roleService;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * Returns the roles for a given user
     *
     * @param User $user
     *
     * @return Role[]
     */
    public function getRolesForUser(User $user)
    {
        $userRoles = $this->userRoleRepository->findRolesForUser($user->getId());

        if(empty($userRoles))
        {
            $userRoles = array($this->roleService->getOrCreateRoleByName('ROLE_DEFAULT_USER'));
        }

        if($user->is_platform_admin())
        {
            $userRoles[] = $this->roleService->getOrCreateRoleByName('ROLE_ADMINISTRATOR');
        }

        return $userRoles;
    }

    /**
     * Checks whether or not a user matches one of the requested roles
     *
     * @param User $user
     * @param Role[] $rolesToMatch
     *
     * @return bool
     */
    public function doesUserHasAtLeastOneRole(User $user, $rolesToMatch = array())
    {
        $userRoles = $this->getRolesForUser($user);
        $userRoleIds = array();

        foreach($userRoles as $userRole)
        {
            $userRoleIds[] = $userRole->getId();
        }

        foreach($rolesToMatch as $roleToMatch)
        {
            if(in_array($roleToMatch->getId(), $userRoleIds))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the users that are attached to a given role
     *
     * @param string $roleName
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsersForRole($roleName)
    {
        $role = $this->roleService->getRoleByName($roleName);
        return $this->userRoleRepository->findUsersForRole($role->getId());
    }

    /**
     * Adds a role to a given user by a given role name
     *
     * @param User $user
     * @param string $roleName
     *
     * @throws \Exception
     */
    public function addRoleForUser(User $user, $roleName)
    {
        $role = $this->roleService->getOrCreateRoleByName($roleName);
        
        $userRoleRelation = new RoleRelation();

        $userRoleRelation->setRoleId($role->getId());
        $userRoleRelation->setUserId($user->getId());

        if(!$userRoleRelation->create())
        {
            throw new \Exception('User role not deleted for user ' . $user->get_fullname() . ' with role ' . $roleName);
        }
    }

    /**
     * Removes a role by a given name from a given user
     *
     * @param User $user
     * @param string $roleName
     *
     * @throws \Exception
     */
    public function removeRoleFromUser(User $user, $roleName)
    {
        try
        {
            $role = $this->roleService->getRoleByName($roleName);
        }
        catch(\Exception $ex)
        {
            return;
        }

        $userRoleRelation = $this->userRoleRepository->findUserRoleRelationByRoleAndUser(
            $role->getId(), $user->getId()
        );

        if(!$userRoleRelation)
        {
            return;
        }

        if(!$userRoleRelation->delete())
        {
            throw new \Exception('User role not deleted for user ' . $user->get_fullname() . ' with role ' . $roleName);
        }
    }
}