<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\User\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Authentication\AuthenticationValidator;

class Login extends Block
{

    public function is_editable()
    {
        return false;
    }

    public function is_hidable()
    {
        return false;
    }

    public function is_deletable()
    {
        return false;
    }

    public function display_content()
    {
        $html = array();

        if (! $this->get_user() || ($this->get_user() instanceof \Chamilo\Core\User\Storage\DataClass\User &&
             $this->get_user()->is_anonymous_user()))
        {
            $request = $this->get_parent()->getApplication()->getApplicationConfiguration()->getRequest();
            $message = $request->query->get(AuthenticationValidator :: PARAM_AUTHENTICATION_ERROR);

            if ($message)
            {
                $html[] = '<div class="error-message" style="width: auto; left: 0%; right: 0%; margin: auto;">' .
                     $message . '</div>';
            }

            $html[] = $this->display_login_form();

            if (! PlatformSetting :: get('allow_registration', \Chamilo\Core\User\Manager :: context()))
            {
                // add custom info here if you do not allow registration (if you use LDAP...)
                // $html[] = "<p>Helpdesk:</p>";
            }
        }
        else
        {
            $user = $this->get_user();

            $profilePhotoUrl = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                    \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $user->get_id()));

            $html[] = '<br />';
            $max_height = PlatformSetting :: get('restrict_picture_height', \Chamilo\Core\User\Manager :: context()) ? 'max-height:100px' : null;
            $html[] = '<img src="' . htmlspecialchars($profilePhotoUrl->getUrl()) . '" style="max-width: 100%; ' .
                 $max_height . '" />';
            $html[] = '<br /><br />';
            $html[] = htmlspecialchars($user->get_fullname()) . '<br />';
            $html[] = htmlspecialchars($user->get_email()) . '<br />';
            $html[] = '<br /><br />';

            $redirect = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_LOGOUT));
            $logout_link = $redirect->getUrl();

            $html[] = '<a href="' . $logout_link . '" class="button normal_button logout_button">' . htmlspecialchars(
                Translation :: get('Logout')) . '</a>';
            // add custom change password url if you are using external authentication (LDAP...)
            // $html[] = '<br /><br /><a href="https://yourchangepassurl/" target="password" class="button normal_button
            // register_button">' . htmlspecialchars(Translation :: get('ChangePassword')) . '</a>';
            $html[] = '<br /><br />';
        }

        return implode(PHP_EOL, $html);
    }

    public function display_login_form()
    {
        $request = $this->get_parent()->getApplication()->getApplicationConfiguration()->getRequest();

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_LOGIN,
                'noExtAuth' => $request->query->get('noExtAuth')));
        $login_link = $redirect->getUrl();

        $form = new FormValidator('formLogin', 'post', $login_link);
        $renderer = & $form->defaultRenderer();
        $renderer->setElementTemplate('<div class="row">{label}<br />{element}</div>');
        $form->setRequiredNote(null);
        $html = '<script type="text/javascript">$(document).ready(function(){document.formLogin.login.focus();});</script>';
        $form->addElement('html', $html);
        $form->addElement('text', 'login', Translation :: get('UserName'), array('style' => 'width: 90%;'));
        $form->addRule(
            'login',
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');
        $form->addElement('password', 'password', Translation :: get('Password'), array('style' => 'width: 90%;'));
        $form->addRule(
            'password',
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $buttons = array();
        $buttons[] = $form->createElement(
            'style_submit_button',
            'submitAuth',
            Translation :: get('Login'),
            array('class' => 'positive login'));

        if (PlatformSetting :: get('allow_registration', \Chamilo\Core\User\Manager :: context()) || PlatformSetting :: get(
            'allow_password_retrieval',
            \Chamilo\Core\User\Manager :: context()))
        {
            if (PlatformSetting :: get('allow_registration', \Chamilo\Core\User\Manager :: context()))
            {
                $redirect = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                        Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_REGISTER_USER));
                $link = $redirect->getUrl();

                $buttons[] = $form->createElement(
                    'static',
                    null,
                    null,
                    '<a href="' . htmlspecialchars($link) . '" class="button normal_button register_button">' . htmlspecialchars(
                        Translation :: get('Reg')) . '</a>');
            }
            if (PlatformSetting :: get('allow_password_retrieval', \Chamilo\Core\User\Manager :: context()))
            {
                $redirect = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                        Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_RESET_PASSWORD));
                $link = $redirect->getUrl();

                $buttons[] = $form->createElement(
                    'static',
                    null,
                    null,
                    '<a href="' . htmlspecialchars($link) . '" class="button normal_button help_button">' . htmlspecialchars(
                        Translation :: get('ResetPassword')) . '</a>');
            }
        }

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        return $form->toHtml();
    }
}
