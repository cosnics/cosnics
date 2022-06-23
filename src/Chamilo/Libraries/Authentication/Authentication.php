<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Authentication
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Authentication implements AuthenticationInterface
{
    public const PARAM_LOGIN = 'login';
    public const PARAM_PASSWORD = 'password';

    protected ConfigurationConsulter $configurationConsulter;

    protected ChamiloRequest $request;

    protected Translator $translator;

    protected UserService $userService;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->request = $request;
        $this->userService = $userService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    protected function getUserFromCredentialsRequest(): ?User
    {
        $username = $this->request->getFromPost(self::PARAM_LOGIN);

        if (empty($username))
        {
            return null;
        }

        $user = $this->userService->getUserByUsernameOrEmail($username);
        if (!$user instanceof User)
        {
            throw new AuthenticationException($this->translator->trans('InvalidUsername', [], 'Chamilo\Libraries'));
        }

        if ($user->getAuthenticationSource() != $this->getAuthenticationType())
        {
            return null;
        }

        if (!$this->isAuthSourceActive())
        {
            throw new AuthenticationException($this->translator->trans('AuthSourceNotActive', [], 'Chamilo\Libraries'));
        }

        return $user;
    }

    protected function isAuthSourceActive(): bool
    {
        return (bool) $this->configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'enable' . str_replace('\\', '', $this->getAuthenticationType())]
        );
    }

}
