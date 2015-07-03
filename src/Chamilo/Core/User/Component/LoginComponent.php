<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserLoginSession;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package user.lib.user_manager.component
 * @author Sven Vanpoucke
 */
class LoginComponent extends Manager implements NoAuthenticationSupport
{
    const PARAM_LOGIN = 'login';
    const PARAM_PASSWORD = 'password';
    const PARAM_LOGIN_FAILED = 'loginFailed';
    const PARAM_REQUEST_URI = 'request_uri';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $login = Request :: post(self :: PARAM_LOGIN);
        $password = Request :: post(self :: PARAM_PASSWORD);
        $user = \Chamilo\Core\User\Storage\DataManager :: login($login, $password);

        if ($user instanceof User)
        {
            \Chamilo\Libraries\Platform\Session\Session :: register('_uid', $user->get_id());
            Event :: trigger('login', Manager :: context(), array('server' => $_SERVER, 'user' => $user));

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
        else
        {
            \Chamilo\Libraries\Platform\Session\Session :: unregister('_uid');
            $parameters = array(self :: PARAM_LOGIN_FAILED => true);

            $redirect = new Redirect($parameters);
            $redirect->toUrl();
        }
    }
}
