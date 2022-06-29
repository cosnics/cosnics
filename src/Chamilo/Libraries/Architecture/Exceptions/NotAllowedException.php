<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class NotAllowedException extends UserException
{

    public function __construct(bool $showLoginForm = false)
    {
        $this->initializeContainer();
        $this->getSessionUtilities()->register('request_uri', $_SERVER['REQUEST_URI']);

        $html = [];

        $html[] = $this->getTranslator()->trans('NotAllowed', [], StringUtilities::LIBRARIES);

        parent::__construct(implode(PHP_EOL, $html));
    }

    public function getLoginForm(): FormValidator
    {
        $translator = $this->getTranslator();

        $form = new FormValidator('formLogin', FormValidator::FORM_METHOD_POST, $this->getRequest()->getUri());

        $form->get_renderer()->setElementTemplate('{element}');

        $form->setRequiredNote(null);

        $form->addElement('html', '<div class="form-group">');
        $form->addElement('html', '<div class="input-group">');

        $form->addElement(
            'html', '<div class="input-group-addon">' . $translator->trans('Username', [], StringUtilities::LIBRARIES) .
            '</div>'
        );

        $form->addElement(
            'text', 'login', $translator->trans('UserName', [], StringUtilities::LIBRARIES),
            ['size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control']
        );

        $form->addElement('html', '</div>');
        $form->addElement('html', '</div>');

        $form->addElement('html', '<div class="form-group">');
        $form->addElement('html', '<div class="input-group">');

        $form->addElement(
            'html', '<div class="input-group-addon">' . $translator->trans('Password', [], StringUtilities::LIBRARIES) .
            '</div>'
        );

        $form->addElement(
            'password', 'password', $translator->trans('Pass', [], StringUtilities::LIBRARIES),
            ['size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control']
        );

        $form->addElement('html', '</div>');
        $form->addElement('html', '</div>');

        $form->addElement('html', '<div class="form-group text-right">');
        $form->addElement(
            'style_submit_button', 'submitAuth', $translator->trans('Login', [], StringUtilities::LIBRARIES), null,
            null, new FontAwesomeGlyph('sign-in-alt')
        );
        $form->addElement('html', '</div>');

        $form->addRule(
            'password', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );

        $form->addRule(
            'login', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );

        return $form;
    }
}
