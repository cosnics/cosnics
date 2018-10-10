<?php

namespace Chamilo\Libraries\Authentication\Anonymous;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\Translation\Translator;

/**
 * Authentication method for anonymous users.
 * You can activate the anonymous authentication in the administrator settings. You can define the (custom) domain
 * from which the anonymous authentication is allowed.
 * The authentication will redirect new anonymous users to the landing page in the AnonymousAccessComponent to
 * create a new anonymous user record. The user will receive a cookie so the same user record can be used for the
 * same anonymous user. This will make sure that the users are not constantly redirected to the landing page.
 *
 * @package Chamilo\Libraries\Authentication\Anonymous
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AnonymousAuthentication extends Authentication implements AuthenticationInterface
{
    /**
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected $sessionUtilities;

    /**
     * Authentication constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, SessionUtilities $sessionUtilities
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function login()
    {
        if(!$this->isAuthSourceActive())
        {
            return null;
        }

        $allowedAnonymousAuthenticationUrl = $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'anonymous_authentication_url')
        );

        $allowedAnonymousAuthenticationUrl = str_replace('http://', '', $allowedAnonymousAuthenticationUrl);
        $allowedAnonymousAuthenticationUrl = str_replace('https://', '', $allowedAnonymousAuthenticationUrl);

        $baseUrl = $this->request->server->get('SERVER_NAME');
        if (strpos($allowedAnonymousAuthenticationUrl, $baseUrl) !== 0)
        {
            return null;
        }

        $user = $this->retrieveUserFromCookie();

        if ($user instanceof User)
        {
            return $user;
        }

        $requestedUrlParameters = $this->request->query->all();
        $this->sessionUtilities->register('requested_url_parameters', $requestedUrlParameters);

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_ACCESS_ANONYMOUSLY
            )
        );

        $redirect->toUrl();

        return null;
    }

    public function logout(User $user)
    {

    }

    /**
     * Retrieves a user from a cookie
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    protected function retrieveUserFromCookie()
    {
        $securityToken = $this->request->cookies->get(md5('anonymous_authentication'));
        $user = null;

        if (!empty($securityToken))
        {
            $user = $this->userService->getUserBySecurityToken($securityToken);
        }

        return $user;
    }

    /**
     * Returns the priority of the authentication, lower priorities come first
     *
     * @return int
     */
    public function getPriority()
    {
        return 400;
    }

    /**
     * Returns the short name of the authentication to check in the settings
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'Anonymous';
    }
}