<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\UserLoginSession;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Authentication\AuthenticationException;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LoginComponent extends Manager implements NoAuthenticationSupport
{
    const PARAM_LOGIN = 'login';
    const PARAM_PASSWORD = 'password';
    const PARAM_REQUEST_URI = 'request_uri';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $userName = $this->getRequest()->request->get(self :: PARAM_LOGIN);
        $password = $this->getRequest()->request->get(self :: PARAM_PASSWORD);

        $authenticationValidator = new AuthenticationValidator($this->getRequest(), Configuration :: get_instance());

        try
        {
            $user = $authenticationValidator->performCredentialsAuthentication($userName, $password);

            if (PlatformSetting :: get('enable_terms_and_conditions', self :: context()) &&
                 ! $user->terms_conditions_uptodate())
            {
                $redirect = new Redirect(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_TERMSCONDITIONS));
                $redirect->toUrl();
            }

            if (PlatformSetting :: get('prevent_double_login', self :: context()))
            {
                UserLoginSession :: check_single_login();
            }

            $request_uri = \Chamilo\Libraries\Platform\Session\Session :: retrieve(self :: PARAM_REQUEST_URI);

            if ($request_uri)
            {
                $request_uris = explode("/", $request_uri);
                $request_uri = array_pop($request_uris);
                \Chamilo\Libraries\Platform\Session\Session :: unregister(self :: PARAM_REQUEST_URI);

                $response = new RedirectResponse($request_uri);
                $response->send();
            }

            $parameters = array(Application :: PARAM_CONTEXT => PlatformSetting :: get('page_after_login'));

            $redirect = new Redirect($parameters);
            $redirect->toUrl();
        }
        catch (AuthenticationException $exception)
        {
            \Chamilo\Libraries\Platform\Session\Session :: unregister('_uid');
            $parameters = array(AuthenticationValidator :: PARAM_AUTHENTICATION_ERROR => $exception->getMessage());

            $redirect = new Redirect($parameters);
            $redirect->toUrl();
        }
    }
}
