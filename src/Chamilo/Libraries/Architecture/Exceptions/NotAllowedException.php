<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 */
class NotAllowedException extends \Exception
{

    public function __construct($show_login_form = true)
    {
        Session :: register('request_uri', $_SERVER['REQUEST_URI']);

        $html = array();

        $html[] = Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES);

        if ($show_login_form)
        {
            $html[] = $this->getLoginForm()->toHtml();
        }

        parent :: __construct(implode(PHP_EOL, $html));
    }

    public function getLoginForm()
    {
        $form = new FormValidator(
            'formLogin',
            'post',
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_LOGIN)));

        $form->get_renderer()->setElementTemplate('<div class="row">{element}</div>');

        $form->setRequiredNote(null);
        $form->addElement(
            'text',
            'login',
            Translation :: get('UserName'),
            array('size' => 20, 'onclick' => 'this.value=\'\';'));
        $form->addRule(
            'login',
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');
        $form->addElement(
            'password',
            'password',
            Translation :: get('Pass'),
            array('size' => 20, 'onclick' => 'this.value=\'\';'));
        $form->addRule('password', Translation :: get('ThisFieldIsRequired'), 'required');
        $form->addElement(
            'style_submit_button',
            'submitAuth',
            Translation :: get('Login'),
            array('class' => 'positive login'));
        $form->setDefaults(array('login' => Translation :: get('Username'), 'password' => '*******'));

        return $form;
    }
}
