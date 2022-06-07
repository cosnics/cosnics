<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package user.lib.user_manager.component
 */
class MultiPasswordResetterComponent extends Manager
{

    /**
     *
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    public function getHashingUtilities()
    {
        return $this->getService(HashingUtilities::class);
    }

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = $this->getRequest()->get(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $ids);

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
                $user = DataManager::retrieve_by_id(
                    User::class,
                    (int) $id);

                $password = Text::generate_password();
                $user->set_password($this->getHashingUtilities()->hashString($password));

                if ($user->update())
                {
                    $mail_subject = Translation::get('LoginRequest');
                    $mail_body = [];

                    $mail_body[] = $user->get_fullname() . ',';
                    $mail_body[] = Translation::get('YourAccountParam') . ' ' . Path::getInstance()->getBasePath(true);
                    $mail_body[] = Translation::get('UserName') . ' :' . $user->get_username();
                    $mail_body[] = Translation::get('Password') . ' :' . $password;

                    $mail_body = implode(PHP_EOL, $mail_body);

                    $mail = new Mail($mail_subject, $mail_body, $user->get_email());

                    $mailerFactory = new MailerFactory(Configuration::getInstance());
                    $mailer = $mailerFactory->getActiveMailer();

                    try
                    {
                        $mailer->sendMail($mail);
                    }
                    catch (Exception $ex)
                    {
                    }

                    Event::trigger(
                        'Update',
                        Manager::context(),
                        array(
                            ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                            ChangesTracker::PROPERTY_USER_ID => $this->get_user()->get_id()));
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

            $this->redirect($message, ($failures > 0), array(Application::PARAM_ACTION => self::ACTION_BROWSE_USERS));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('User')),
                        StringUtilities::LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_USERS)),
                Translation::get('AdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_password_resetter');
    }
}
