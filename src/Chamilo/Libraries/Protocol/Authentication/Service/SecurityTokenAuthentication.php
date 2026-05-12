<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Trait\DefaultRedirectAfterLoginTrait;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SecurityTokenAuthentication extends Authentication implements AuthenticationInterface
{
    use DefaultRedirectAfterLoginTrait;

    public function __construct(
        Translator $translator, ChamiloRequest $request, UserService $userService,
        AuthenticationValidator $authenticationValidator, protected UrlGenerator $urlGenerator
    )
    {
        parent::__construct($translator, $request, $userService, $authenticationValidator);
    }

    public function getPriority(): int
    {
        return 300;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    public function login(bool $checkIfAuthenticationSourceIsEnabled = true): ?User
    {
        $this->checkAuthenticationSource($checkIfAuthenticationSourceIsEnabled);

        $securityToken = $this->request->query->get(User::PROPERTY_SECURITY_TOKEN);

        if ($securityToken) {
            try {
                return $this->userService->retrieveUserBySecurityToken($securityToken);
            }
            catch (Throwable) {
                throw new NotAuthenticatedException(
                    $this->translator->trans('InvalidSecurityToken', [], StringUtilities::LIBRARIES)
                );
            }
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
