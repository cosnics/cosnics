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
    const PARAM_LOGIN = 'login';
    const PARAM_PASSWORD = 'password';

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    protected $request;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * Authentication constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
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

    /**
     * @return bool
     */
    protected function isAuthSourceActive()
    {
        return (bool) $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'enable' . $this->getAuthenticationType() . 'Authentication')
        );
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User|null
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    protected function getUserFromCredentialsRequest()
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

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

}
