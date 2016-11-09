<?php

namespace Chamilo\Libraries\Authentication\Anonymous;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\QueryAuthentication;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * Authentication method for anonymous users.
 *
 * You can activate the anonymous authentication in the administrator settings. You can define the (custom) domain
 * from which the anonymous authentication is allowed.
 *
 * The authentication will redirect new anonymous users to the landing page in the AnonymousAccessComponent to
 * create a new anonymous user record. The user will receive a cookie so the same user record can be used for the
 * same anonymous user. This will make sure that the users are not constantly redirected to the landing page.
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AnonymousAuthentication extends QueryAuthentication
{
    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws AuthenticationException
     */
    public function login()
    {
        $allowedAnonymousAuthenticationUrl = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'anonymous_authentication_url')
        );

        $baseUrl = $this->getRequest()->server->get('SERVER_NAME');
        if(strpos($allowedAnonymousAuthenticationUrl, $baseUrl) === false)
        {
            return null;
        }

        $user = $this->retrieveUserFromCookie();

        if ($user instanceof User)
        {
            return $user;
        }

        $requestedUrlParameters = $this->getRequest()->query->all();
        Session::register('requested_url_parameters', $requestedUrlParameters);

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_ACCESS_ANONYMOUSLY
            )
        );

        $redirect->toUrl();
    }

    /**
     * Retrieves a user from a cookie
     */
    protected function retrieveUserFromCookie()
    {
        $securityToken = $this->getRequest()->cookies->get(md5('anonymous_authentication'));
        $user = null;

        if (!empty($securityToken))
        {
            try
            {
                return $this->retrieveUserBySecurityToken($securityToken);
            }
            catch(\Exception $ex) {}
        }

        return $user;
    }
}