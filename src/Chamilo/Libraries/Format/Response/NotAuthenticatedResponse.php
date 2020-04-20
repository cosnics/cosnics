<?php
namespace Chamilo\Libraries\Format\Response;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NotAuthenticatedResponse extends Response
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $page = Page::getInstance();

        $html = array();

        $html[] = $page->getHeader()->render();
        $html[] = $this->renderPanel();
        $html[] = $page->getFooter()->render();

        parent::__construct('', implode(PHP_EOL, $html));
    }

    /**
     * Displays the login form
     *
     * @return string
     */
    public function displayLoginForm()
    {
        $translator = Translation::getInstance();
        $redirect = new Redirect();

        $form = new FormValidator('formLogin', FormValidator::FORM_METHOD_POST, $redirect->getCurrentUrl());

        $form->get_renderer()->setElementTemplate('{element}');
        $form->get_renderer()->setRequiredNoteTemplate(null);

        $form->addElement('html', '<div class="form-group">');
        $form->addElement('html', '<div class="input-group">');

        $form->addElement(
            'html', '<div class="input-group-addon">' . $translator->getTranslation('Username') . '</div>'
        );

        $form->addElement(
            'text', 'login', $translator->getTranslation('UserName'),
            array('size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control')
        );

        $form->addElement('html', '</div>');
        $form->addElement('html', '</div>');

        $form->addElement('html', '<div class="form-group">');
        $form->addElement('html', '<div class="input-group">');

        $form->addElement(
            'html', '<div class="input-group-addon">' . $translator->getTranslation('Password') . '</div>'
        );

        $form->addElement(
            'password', 'password', $translator->getTranslation('Pass'),
            array('size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control')
        );

        $form->addElement('html', '</div>');
        $form->addElement('html', '</div>');

        $form->addElement('html', '<div class="form-group text-right">');
        $form->addElement(
            'style_submit_button', 'submitAuth', $translator->getTranslation('Login'), null, null,
            new FontAwesomeGlyph('sign-in-alt')
        );
        $form->addElement('html', '</div>');

        $form->addRule('password', $translator->getTranslation('ThisFieldIsRequired'), 'required');
        $form->addRule(
            'login', $translator->getTranslation('ThisFieldIsRequired', array(), Utilities::COMMON_LIBRARIES),
            'required'
        );

        return $form->render();
    }

    /**
     * Renders the panel with the not authenticated message and the login form
     *
     * @return string
     */
    public function renderPanel()
    {
        $html = array();

        $html[] = '<div class="row">';

        $html[] = '<div class="col-xs-12 col-md-2 col-lg-3"></div>';

        $html[] = '<div class="col-xs-12 col-md-8 col-lg-6">';
        $html[] = '<div class="panel panel-danger">';
        $html[] = '<div class="panel-heading">';
        $html[] = Translation::get('NotAuthenticated', array(), Utilities::COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $this->displayLoginForm();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-2 col-lg-3"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}