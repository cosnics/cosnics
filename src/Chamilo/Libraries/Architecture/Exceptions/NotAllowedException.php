<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 */
class NotAllowedException extends \Exception
{

    public function __construct($show_login_form = false)
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
        $redirect = new Redirect();

        $form = new FormValidator('formLogin', 'post', $redirect->getCurrentUrl());

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
        $form->addElement('style_submit_button', 'submitAuth', Translation :: get('Login'), null, null, 'log-in');
        $form->setDefaults(array('login' => Translation :: get('Username'), 'password' => '*******'));

        return $form;
    }
}
