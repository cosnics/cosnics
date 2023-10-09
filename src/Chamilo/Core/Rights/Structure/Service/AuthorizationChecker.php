<?php
namespace Chamilo\Core\Rights\Structure\Service;

use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationRoleServiceInterface;
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
     *
     * @var UserRoleServiceInterface
     */
    protected $userRoleService;

    /**
     *
     * @var StructureLocationRoleServiceInterface
     */
    protected $structureLocationRoleService;

    /**
     * AuthorizationChecker constructor.
     * 
     * @param UserRoleServiceInterface $userRoleService
     * @param StructureLocationRoleServiceInterface $structureLocationRoleService
     */
    public function __construct(UserRoleServiceInterface $userRoleService, 
        StructureLocationRoleServiceInterface $structureLocationRoleService)
    {
        $this->userRoleService = $userRoleService;
        $this->structureLocationRoleService = $structureLocationRoleService;
    }

    /**
     * Returns whether or not a user is authorized to view a certain action in a certain context.
     * When no roles
     * are defined on the given location then by default, every user is authorized
     * 
     * @param User $user
     * @param string $context
     * @param string $action
     *
     * @return boolean
     */
    public function isAuthorized(User $user, $context, $action = null)
    {
        $locationRoles = $this->structureLocationRoleService->getRolesForLocationByContextAndAction($context, $action);
        
        if (empty($locationRoles))
        {
            return true;
        }
        
        return $this->userRoleService->doesUserHaveAtLeastOneRole($user, $locationRoles);
    }

    /**
     * Checks the authorization for the user in the given context / action and throws an exception if necessary
     * 
     * @param User $user
     * @param string $context
     * @param string $action
     *
     * @throws NotAllowedException
     */
    public function checkAuthorization(User $user, $context, $action = null)
    {
        if (! $this->isAuthorized($user, $context, $action))
        {
            throw new NotAllowedException();
        }
    }
}