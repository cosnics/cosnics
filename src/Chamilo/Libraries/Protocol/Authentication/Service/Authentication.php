<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Authentication
{
    public const PARAM_LOGIN = 'login';
    public const PARAM_PASSWORD = 'password';

    protected AuthenticationValidator $authenticationValidator;

    protected ChamiloRequest $request;

    protected Translator $translator;

    protected UserService $userService;

    public function __construct(
        Translator $translator, ChamiloRequest $request, UserService $userService,
        AuthenticationValidator $authenticationValidator
    )
    {
        $this->translator = $translator;
        $this->request = $request;
        $this->userService = $userService;
        $this->authenticationValidator = $authenticationValidator;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     */
    public function checkAuthenticationSource(bool $checkIfAuthenticationSourceIsEnabled = true): void
    {
        if ($checkIfAuthenticationSourceIsEnabled &&
            !$this->getAuthenticationValidator()->isSourceEnabled(static::class)) {
            throw new AuthenticationException(
                $this->getTranslator()->trans('AuthSourceNotActive', [], StringUtilities::LIBRARIES)
            );
        }
    }

    public function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->authenticationValidator;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getUserFromCredentialsRequest(): ?User
    {
        $translator = $this->getTranslator();

        $username = $this->getRequest()->request->get(self::PARAM_LOGIN);

        if (empty($username)) {
            return null;
        }

        $user = $this->getUserService()->getUserByUsernameOrEmail($username);

        if (!$user instanceof User) {
            throw new AuthenticationException(
                $translator->trans('InvalidUsername', [], StringUtilities::LIBRARIES)
            );
        }

        if ($user->getAuthenticationSource() != static::class) {
            return null;
        }

        return $user;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}
