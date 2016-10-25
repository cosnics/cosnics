<?php
namespace Chamilo\Core\Rights\Structure\Service\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Service that is used for authorization checks
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AuthorizationCheckerInterface
{
    /**
     * Returns whether or not a user is authorized to view a certain action in a certain context
     *
     * @param User $user
     * @param $context
     * @param $action
     */
    public function isAuthorized(User $user, $context, $action = null);

    /**
     * Checks the authorization for the user in the given context / action and throws an exception if necessary
     *
     * @param User $user
     * @param string $context
     * @param string $action
     *
     * @throws NotAllowedException
     */
    public function checkAuthorization(User $user, $context, $action = null);
}