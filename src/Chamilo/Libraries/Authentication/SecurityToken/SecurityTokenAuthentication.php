<?php
namespace Chamilo\Libraries\Authentication\SecurityToken;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Authentication\SecurityToken$SecurityTokenAuthentication
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SecurityTokenAuthentication extends Authentication implements AuthenticationInterface
{
    protected bool $disableAuthSourceCheck = false;

    /**
     * Disables the check if the auth source is active or not (used to make sure that this can run for certain
     * components only)
     */
    public function disableAuthSourceCheck()
    {
        $this->disableAuthSourceCheck = true;
    }

    public function getAuthenticationType(): string
    {
        return __NAMESPACE__;
    }

    public function getPriority(): int
    {
        return 300;
    }

    /**
     * @throws AuthenticationException
     */
    public function login(): ?User
    {
        if (!$this->disableAuthSourceCheck && !$this->isAuthSourceActive())
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
                    $this->translator->trans('InvalidSecurityToken', [], StringUtilities::LIBRARIES)
                );
            }

            return $user;
        }

        return null;
    }

    public function logout(User $user)
    {

    }
}
