<?php

namespace Chamilo\Libraries\Authentication\SecurityToken;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Authentication\QueryAuthentication;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

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
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws AuthenticationException
     */
    public function login()
    {
        if(!$this->isAuthSourceActive())
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
        return 100;
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
