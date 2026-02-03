<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SecurityTokenAuthentication extends Authentication implements AuthenticationInterface
{
    protected bool $disableAuthSourceCheck = false;

    /**
     * Disables the check if the auth source is active or not (used to make sure that this can run for certain
     * components only)
     */
    public function disableAuthSourceCheck(): void
    {
        $this->disableAuthSourceCheck = true;
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
        if (!$this->disableAuthSourceCheck && !$this->isAuthSourceActive()) {
            return null;
        }

        $securityToken = $this->getRequest()->query->get(User::PROPERTY_SECURITY_TOKEN);

        if ($securityToken) {
            $user = $this->getUserService()->getUserBySecurityToken($securityToken);

            if (!$user instanceof User) {
                throw new AuthenticationException(
                    $this->getTranslator()->trans('InvalidSecurityToken', [], StringUtilities::LIBRARIES)
                );
            }

            return $user;
        }

        return null;
    }

    public function logout(User $user): void
    {
    }
}
