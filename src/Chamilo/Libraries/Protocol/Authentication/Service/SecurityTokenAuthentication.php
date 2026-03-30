<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
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
    public function getPriority(): int
    {
        return 300;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    public function login(bool $checkIfAuthenticationSourceIsEnabled = true): ?User
    {
        $this->checkAuthenticationSource($checkIfAuthenticationSourceIsEnabled);

        $securityToken = $this->request->query->get(User::PROPERTY_SECURITY_TOKEN);

        if ($securityToken) {
            $user = $this->userService->getUserBySecurityToken($securityToken);

            if (!$user instanceof User) {
                throw new NotAuthenticatedException(
                    $this->translator->trans('InvalidSecurityToken', [], StringUtilities::LIBRARIES)
                );
            }

            return $user;
        }
        else {
            throw new NotAuthenticatedException(
                $this->translator->trans('NoSecurityToken', [], StringUtilities::LIBRARIES)
            );
        }
    }

    public function logout(User $user): void
    {
    }
}
