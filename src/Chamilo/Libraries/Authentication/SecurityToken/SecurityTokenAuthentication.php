<?php

namespace Chamilo\Libraries\Authentication\SecurityToken;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;

/**
 *
 * @package Chamilo\Libraries\Authentication\SecurityToken$SecurityTokenAuthentication
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SecurityTokenAuthentication extends Authentication implements AuthenticationInterface
{
    /**
     * @var bool
     */
    protected $disableAuthSourceCheck = false;

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws AuthenticationException
     */
    public function login()
    {
        if(!$this->disableAuthSourceCheck && !$this->isAuthSourceActive())
        {
            return null;
        }

        $securityToken = $this->request->query->get(User::PROPERTY_SECURITY_TOKEN);

        if ($securityToken)
        {
            $user = $this->userService->getUserBySecurityToken($securityToken);

            if (!$user instanceof User)
            {
                throw new AuthenticationException(
                    $this->translator->trans('InvalidSecurityToken', [], 'Chamilo\Libraries')
                );
            }

            return $user;
        }

        return null;
    }

    /**
     * Disables the check if the auth source is active or not (used to make sure that this can run for certain components only)
     */
    public function disableAuthSourceCheck()
    {
        $this->disableAuthSourceCheck = true;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user)
    {

    }

    /**
     * Returns the priority of the authentication, lower priorities come first
     *
     * @return int
     */
    public function getPriority()
    {
        return 300;
    }

    /**
     * Returns the short name of the authentication to check in the settings
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'SecurityToken';
    }
}
