<?php
namespace Chamilo\Core\User\Roles\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\RoleRelation;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Exception;

/**
 * Manages roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRoleService implements UserRoleServiceInterface
{

    /**
     *
     * @var RoleServiceInterface
     */
    protected $roleService;

    /**
     *
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

        if (!$this->userRoleRepository->create($userRoleRelation))
        {
            throw new Exception('User role not created for user ' . $user->get_fullname() . ' with role ' . $roleName);
        }
    }

    /**
     * Checks whether or not a user matches one of the requested roles
     *
     * @param User $user
     * @param Role[] $rolesToMatch
     *
     * @return bool
     */
    public function doesUserHasAtLeastOneRole(User $user, $rolesToMatch = [])
    {

        $userRoles = $this->getRolesForUser($user);
        //        var_dump($user, $rolesToMatch, $userRoles);
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

    /**
     * Returns the roles for a given user
     *
     * @param User $user
     *
     * @return \Chamilo\Core\User\Roles\Storage\DataClass\Role[]
     */
    public function getRolesForUser(User $user)
    {
        $userRoles = $this->userRoleRepository->findRolesForUser($user->getId());

        if ($userRoles->count() == 0)
        {
            $userRoles = array($this->roleService->getOrCreateRoleByName('ROLE_DEFAULT_USER'));
        }

        if ($user->is_platform_admin())
        {
            $userRoles[] = $this->roleService->getOrCreateRoleByName('ROLE_ADMINISTRATOR');
        }

        return $userRoles;
    }

    /**
     * Returns the users that are attached to a given role
     *
     * @param string $roleName
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Exception
     */
    public function getUsersForRole($roleName)
    {
        $role = $this->roleService->getRoleByName($roleName);

        return $this->userRoleRepository->findUsersForRole($role->getId());
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
        catch (Exception $ex)
        {
            return;
        }

        $userRoleRelation =
            $this->userRoleRepository->findUserRoleRelationByRoleAndUser($role->getId(), $user->getId());

        if (!$userRoleRelation)
        {
            return;
        }

        if (!$this->userRoleRepository->delete($userRoleRelation))
        {
            throw new Exception('User role not deleted for user ' . $user->get_fullname() . ' with role ' . $roleName);
        }
    }
}