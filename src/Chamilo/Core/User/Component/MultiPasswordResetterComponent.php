<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class MultiPasswordResetterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request :: get(self :: PARAM_USER_USER_ID);

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    (int) $id);

                $password = Text :: generate_password();
                $user->set_password(Hashing :: hash($password));

                if ($user->update())
                {
                    $mail_subject = Translation :: get('LoginRequest');
                    $mail_body[] = $user->get_fullname() . ',';
                    $mail_body[] = Translation :: get('YourAccountParam') . ' ' .
                         Path :: getInstance()->getBasePath(true);
                    $mail_body[] = Translation :: get('UserName') . ' :' . $user->get_username();
                    $mail_body[] = Translation :: get('Password') . ' :' . $password;
                    $mail_body = implode(PHP_EOL, $mail_body);
                    $from[Mail :: EMAIL] = (PlatformSetting :: get('no_reply_email') != '') ? PlatformSetting :: get(
                        'no_reply_email') : PlatformSetting :: get('administrator_email');
                    $mail = Mail :: factory($mail_subject, $mail_body, $user->get_email(), $from);
                    $mail->send();

                    Event :: trigger(
                        'Update',
                        Manager :: context(),
                        array(
                            ChangesTracker :: PROPERTY_REFERENCE_ID => $user->get_id(),
                            ChangesTracker :: PROPERTY_USER_ID => $this->get_user()->get_id()));
                }
                else
                {
                    $failures ++;
                }
            }

            $message = $this->get_result(
                $failures,
                count($ids),
                'UserPasswordNotResetted',
                'UserPasswordsNotResetted',
                'UserPasswordResetted',
                'UserPasswordsResetted');

            $this->redirect(
                $message,
                ($failures > 0),
                array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_USERS));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('User')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_USERS)),
                Translation :: get('UserManagerAdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_password_resetter');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_USER_USER_ID);
    }
}
