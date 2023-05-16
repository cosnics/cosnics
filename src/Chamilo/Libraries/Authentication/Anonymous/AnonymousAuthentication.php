<?php
namespace Chamilo\Libraries\Authentication\Anonymous;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    protected SessionUtilities $sessionUtilities;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, SessionUtilities $sessionUtilities, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);

        $this->sessionUtilities = $sessionUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    public function getAuthenticationType(): string
    {
        return __NAMESPACE__;
    }

    public function getPriority(): int
    {
        return 400;
    }

    /**
     * @throws \ReflectionException
     */
    public function login(): ?User
    {
        if (!$this->isAuthSourceActive())
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

        $redirect = new RedirectResponse(
            $this->urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_ACCESS_ANONYMOUSLY
            ])
        );

        $redirect->send();
        exit;
    }

    public function logout(User $user)
    {

    }

    protected function retrieveUserFromCookie(): ?User
    {
        $securityToken = $this->request->cookies->get(md5('anonymous_authentication'));
        $user = null;

        if (!empty($securityToken))
        {
            $user = $this->userService->getUserBySecurityToken($securityToken);
        }

        return $user;
    }
}