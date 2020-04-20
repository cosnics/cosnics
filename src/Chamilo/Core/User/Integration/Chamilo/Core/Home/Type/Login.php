<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Home\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Login extends BlockRenderer
{

    public function displayContent()
    {
        $html = array();

        if (!$this->getUser() || ($this->getUser() instanceof User && $this->getUser()->is_anonymous_user()))
        {
            $request = $this->getRenderer()->getApplicationConfiguration()->getRequest();
            $message = $request->query->get(AuthenticationValidator::PARAM_AUTHENTICATION_ERROR);

            if ($message)
            {
                $html[] =
                    '<div class="error-message" style="width: auto; left: 0%; right: 0%; margin: auto;">' . $message .
                    '</div>';
            }

            $html[] = $this->displayLoginForm();

            if (!Configuration::getInstance()->get_setting(
                array(Manager::context(), 'allow_registration')
            ))
            {
                // add custom info here if you do not allow registration (if you use LDAP...)
                // $html[] = "<p>Helpdesk:</p>";
            }
        }
        else
        {
            $user = $this->getUser();

            $profilePhotoUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                    Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                    Manager::PARAM_USER_USER_ID => $user->get_id()
                )
            );

            $maximumHeight = Configuration::getInstance()->get_setting(
                array(Manager::context(), 'restrict_picture_height')
            ) ? 'max-height:100px' : null;

            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => Manager::context(),
                    Application::PARAM_ACTION => Manager::ACTION_LOGOUT
                )
            );
            $logoutLink = $redirect->getUrl();

            $html[] = '<img src="' . htmlspecialchars($profilePhotoUrl->getUrl()) . '" alt="' .
                htmlspecialchars($user->get_fullname()) . '"  class="img-thumbnail" style="max-width: 100%; ' .
                $maximumHeight . '" />';
            $html[] = '<h3>' . htmlspecialchars($user->get_fullname()) . '</h3>';
            $html[] = '<p>' . htmlspecialchars($user->get_email()) . '</p>';
            $html[] = '<p><a href="' . $logoutLink . '" class="btn btn-danger" role="button">' . htmlspecialchars(
                    Translation::get('Logout')
                ) . '</a></p>';
        }

        return implode(PHP_EOL, $html);
    }

    public function displayLoginForm()
    {
        $request = $this->getRenderer()->getApplicationConfiguration()->getRequest();

        $form = new FormValidator('formLogin', FormValidator::FORM_METHOD_POST);
        $renderer = &$form->defaultRenderer();
        $renderer->setElementTemplate('<div class="form-row">{label}<br />{element}</div>');
        $form->setRequiredNote(null);
        $html = '<script>$(document).ready(function(){document.formLogin.login.focus();});</script>';
        $form->addElement('html', $html);
        $form->addElement('text', 'login', Translation::get('UserName'), array('style' => 'width: 90%;'));
        $form->addRule('login', Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required');
        $form->addElement('password', 'password', Translation::get('Password'), array('style' => 'width: 90%;'));
        $form->addRule(
            'password', Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
        );

        $buttons = array();
        $buttons[] = $form->createElement(
            'style_submit_button', 'submitAuth', Translation::get('Login'), null, null,
            new FontAwesomeGlyph('sign-in-alt')
        );

        if (Configuration::getInstance()->get_setting(
                array(Manager::context(), 'allow_registration')
            ) || Configuration::getInstance()->get_setting(
                array(Manager::context(), 'allow_password_retrieval')
            ))
        {
            if (Configuration::getInstance()->get_setting(
                array(Manager::context(), 'allow_registration')
            ))
            {
                $redirect = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => Manager::context(),
                        Application::PARAM_ACTION => Manager::ACTION_REGISTER_USER
                    )
                );
                $link = $redirect->getUrl();

                $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');

                $buttons[] = $form->createElement(
                    'static', null, null,
                    '<a href="' . htmlspecialchars($link) . '" class="btn btn-default">' . $glyph->render() . ' ' .
                    htmlspecialchars(
                        Translation::get('Reg')
                    ) . '</a>'
                );
            }
            if (Configuration::getInstance()->get_setting(
                array(Manager::context(), 'allow_password_retrieval')
            ))
            {
                $redirect = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => Manager::context(),
                        Application::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD
                    )
                );
                $link = $redirect->getUrl();

                $glyph = new FontAwesomeGlyph('question-circle', array(), null, 'fas');

                $buttons[] = $form->createElement(
                    'static', null, null,
                    '<a href="' . htmlspecialchars($link) . '" class="btn btn-default">' . $glyph->render() . ' ' .
                    htmlspecialchars(
                        Translation::get('ResetPassword')
                    ) . '</a>'
                );
            }
        }

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        return $form->toHtml();
    }

    public function isDeletable()
    {
        return false;
    }

    public function isEditable()
    {
        return false;
    }

    public function isHidable()
    {
        return false;
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }
}
