<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Authentication
{
    public const string PARAM_LOGIN = 'login';
    public const string PARAM_PASSWORD = 'password';
    public const string PARAM_AUTHENTICATION = 'authentication';

    public function __construct(
        protected Translator $translator, protected ChamiloRequest $request, protected UserService $userService,
        protected AuthenticationValidator $authenticationValidator
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    public function checkAuthenticationSource(bool $checkIfAuthenticationSourceIsEnabled = true): void
    {
        if ($checkIfAuthenticationSourceIsEnabled && !$this->authenticationValidator->isSourceEnabled(static::class)) {
            throw new NotAuthenticatedException(
                $this->translator->trans('AuthSourceNotActive', [], StringUtilities::LIBRARIES)
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    protected function getUserFromCredentialsRequest(): ?User
    {
        $username = $this->request->request->get(self::PARAM_LOGIN);

        if (empty($username)) {
            return null;
        }

        $user = $this->userService->getUserByUsernameOrEmail($username);

        if (!$user instanceof User) {
            throw new NotAuthenticatedException(
                $this->translator->trans('InvalidUsername', [], StringUtilities::LIBRARIES)
            );
        }

        if ($user->getAuthenticationSource() != static::class) {
            return null;
        }

        return $user;
    }
}
