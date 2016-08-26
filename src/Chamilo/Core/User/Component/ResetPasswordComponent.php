<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: reset_password.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
/**
 * This component can be used to reset the password of a user.
 * The user will be asked for his email-address and if the
 * authentication source of the user allows password resets, an email with further instructions will be send to the
 * user.
 */
class ResetPasswordComponent extends Manager implements NoAuthenticationSupport
{
    const PARAM_RESET_KEY = 'key';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user_id = \Chamilo\Libraries\Platform\Session\Session :: get_user_id();
        $allow_password_retrieval = PlatformSetting :: get('allow_password_retrieval', self :: context());

        if ($allow_password_retrieval == false)
        {
            throw new NotAllowedException();
        }

        if (isset($user_id))
        {
            return $this->display_error_page(Translation :: get('AlreadyRegistered'));
        }

        $html = array();

        $html[] = $this->render_header();

        $request_key = Request :: get(self :: PARAM_RESET_KEY);
        $request_user_id = Request :: get(User :: PROPERTY_ID);
        if (! is_null($request_key) && ! is_null($request_user_id))
        {

            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                (int) $request_user_id);
            if ($this->get_user_key($user) == $request_key)
            {
                $this->create_new_password($user);
                Event :: trigger(
                    'ResetPassword',
                    Manager :: context(),
                    array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()));
                $html[] = Display :: normal_message('lang_your_password_has_been_emailed_to_you');
            }
            else
            {
                $html[] = Display :: error_message(Translation :: get('InvalidRequest'));
            }
        }
        else
        {
            $form = new FormValidator('lost_password', 'post', $this->get_url());
            $form->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'));
            $form->addRule(
                User :: PROPERTY_EMAIL,
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
                'required');
            $form->addRule(User :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');
            $form->addElement('submit', 'submit', Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES));
            if ($form->validate())
            {
                $values = $form->exportValues();
                $users = \Chamilo\Core\User\Storage\DataManager :: retrieve_users_by_email(
                    $values[User :: PROPERTY_EMAIL]);
                if (count($users) == 0)
                {
                    $html[] = Display :: error_message('NoUserWithThisEmail');
                }
                else
                {
                    $failures = 0;

                    foreach ($users as $index => $user)
                    {
                        $auth_source = $user->get_auth_source();
                        $auth = Authentication :: factory($auth_source);
                        if (! $auth instanceof ChangeablePassword)
                        {
                            $html[] = Display :: error_message('ResetPasswordNotPossibleForThisUser');
                        }
                        else
                        {
                            if (! $this->send_reset_link($user))
                            {
                                $failures ++;
                            }
                        }
                    }

                    $message = $this->get_result(
                        $failures,
                        count($users),
                        'ResetLinkHasNotBeenSend',
                        'ResetLinksHasNotBeenSend',
                        'ResetLinkHasBeenSend',
                        'ResetLinksHasBeenSend');
                    if ($failures == 0)
                    {
                        $html[] = Display :: normal_message($message);
                    }
                    else
                    {
                        $html[] = Display :: error_message($message);
                    }
                }
            }
            else
            {
                $html[] = $form->toHtml();
            }
        }

        $html[] = $this->render_footer();
    }

    /**
     * Creates a new random password for the given user and sends an email to this user with the new password.
     *
     * @param User $user
     * @return boolean True if successfull.
     */
    private function create_new_password($user)
    {
        $password = Text :: generate_password();
        $user->set_password(Hashing :: hash($password));
        $user->update();
        $mail_subject = Translation :: get('LoginRequest');
        $mail_body[] = '<div style="font-family:arial, sans-serif">';
        $mail_body[] = '<p>' . Translation :: get('MailResetPasswordDear', array('USER' => $user->get_fullname())) .
             '</p>';
        $mail_body[] = '<p>' . Translation :: get('MailResetPasswordDoneBody') . '</p>';
        $mail_body[] = '<p>' . Translation :: get('UserName') . ': ' . $user->get_username() . '<br/>';
        $mail_body[] = Translation :: get('MailResetPasswordNew') . ': ' . $password . '</p>';
        $mail_body[] = '<p>' . Translation :: get(
            'MailResetPasswordLogIn',
            array(
                'LOGINLINK' => '<a href="' . Path :: getInstance()->getBasePath(true) . '">' .
                     Path :: getInstance()->getBasePath(true) . '</a>')) . '</p>';
        $mail_body[] = '<p>' . Translation :: get('MailResetPasswordCloser') . '<br/>';
        $mail_body[] = Translation :: get(
            'MailResetPasswordSender',
            array(
                'ADMINFIRSTNAME' => PlatformSetting :: get('administrator_firstname'),
                'ADMINLASTNAME' => PlatformSetting :: get('administrator_surname'))) . '</p>';
        $mail_body[] = '</div>';
        $mail_body = implode(PHP_EOL, $mail_body);


        $mail = new Mail($mail_subject, $mail_body, $user->get_email());

        $mailerFactory = new MailerFactory(Configuration::get_instance());
        $mailer = $mailerFactory->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);

            return true;
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /**
     * Sends an email to the user containing a reset link to request a password change.
     *
     * @param User $user
     * @return boolean True if successfull.
     */
    private function send_reset_link($user)
    {
        $url_params[self :: PARAM_RESET_KEY] = $this->get_user_key($user);
        $url_params[User :: PROPERTY_ID] = $user->get_id();
        $url = $this->get_url($url_params);
        $protocol = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
        $reset_link = $protocol . $_SERVER['HTTP_HOST'] . $url;
        $mail_subject = Translation :: get('LoginRequest');
        $mail_body[] = '<div style="font-family:arial, sans-serif">';
        $mail_body[] = '<p>' . Translation :: get('MailResetPasswordDear', array('USER' => $user->get_fullname())) .
             '</p>';
        $mail_body[] = '<p>' . Translation :: get('MailResetPasswordAskBody') . '</p>';
        $mail_body[] = '<p>' . Translation :: get('UserName') . ': ' . $user->get_username() . '<br/>';
        $mail_body[] = Translation :: get('MailResetPasswordLink') . ': <a href="' . $reset_link . '">' . $reset_link .
             '</a></p>';
        $mail_body[] = '<p>' . Translation :: get('MailResetPasswordCloser') . '<br/>';
        $mail_body[] = Translation :: get(
            'MailResetPasswordSender',
            array(
                'ADMINFIRSTNAME' => PlatformSetting :: get('administrator_firstname'),
                'ADMINLASTNAME' => PlatformSetting :: get('administrator_surname'))) . '</p>';
        $mail_body[] = '</div>';
        $mail_body = implode(PHP_EOL, $mail_body);

        $mail = new Mail($mail_subject, $mail_body, $user->get_email());

        $mailerFactory = new MailerFactory(Configuration::get_instance());
        $mailer = $mailerFactory->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);

            return true;
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /**
     * Creates a key which is used to identify the user
     *
     * @param User $user
     * @return string The requested key
     */
    private function get_user_key($user)
    {
        global $security_key;
        return Hashing :: hash($security_key . $user->get_email());
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_password_resetter');
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
