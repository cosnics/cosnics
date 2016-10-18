<?php

namespace Chamilo\Core\Rights\Structure\Service;

use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Service that is used for authorization checks
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var UserRoleServiceInterface
     */
    protected $userRoleService;

    /**
     * @var StructureLocationRoleService
     */
    protected $structureLocationRoleService;

    /**
     * AuthorizationChecker constructor.
     *
     * @param UserRoleServiceInterface $userRoleService
     * @param StructureLocationRoleService $structureLocationRoleService
     */
    public function __construct(
        UserRoleServiceInterface $userRoleService, StructureLocationRoleService $structureLocationRoleService
    )
    {
        $this->userRoleService = $userRoleService;
        $this->structureLocationRoleService = $structureLocationRoleService;
    }

    /**
     * Returns whether or not a user is authorized to view a certain component in a certain context. When no roles
     * are defined on the given location then by default, every user is authorized
     *
     * @param User $user
     * @param string $context
     * @param string $component
     *
     * @return boolean
     */
    public function isAuthorized(User $user, $context, $component = null)
    {
        $locationRoles = $this->structureLocationRoleService->getRolesForLocationByContextAndComponent(
            $context, $component
        );

        if(empty($locationRoles))
        {
            return true;
        }

        return $this->userRoleService->doesUserHaveRoles($user, $locationRoles);
    }

    /**
     * Checks the authorization for the user in the given context / component and throws an exception if necessary
     *
     * @param User $user
     * @param string $context
     * @param string $component
     *
     * @throws NotAllowedException
     */
    public function checkAuthorization(User $user, $context, $component = null)
    {
        if(!$this->isAuthorized($user, $context, $component))
        {
            throw new NotAllowedException();
        }
    }
}