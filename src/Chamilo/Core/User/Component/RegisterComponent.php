<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\RegisterForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: register.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class RegisterComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $allow_registration = PlatformSetting :: get('allow_registration', self :: context());
        if ($allow_registration == false)
        {
            throw new NotAllowedException();
        }

        $user = new User();
        $user->set_platformadmin(0);
        $user->set_password(1);
        // $user->set_creator_id($user_info['user_id']);

        $form = new RegisterForm($user, $this->get_url());

        if ($form->validate())
        {
            $success = $form->create_user();
            if ($success == 1)
            {
                // $this->redirect(Translation :: get($success ? 'UserRegistered' : 'UserNotRegistered'), ($success ?
                // false : true), array(), array(), false, Redirect :: TYPE_LINK);

                $parameters = array();

                if (PlatformSetting :: get('allow_registration', self :: context()) == 2)
                {
                    $parameters['message'] = Translation :: get('UserAwaitingApproval');
                }

                $parameters[Application :: PARAM_CONTEXT] = '';
                Redirect :: link($parameters);
            }
            else
            {
                Request :: set_get('error_message', Translation :: get('UsernameNotAvailable'));

                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_register');
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
