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
        $html[] = $page->getHeader()->toHtml();
        $html[] = $this->renderPanel();
        $html[] = $page->getFooter()->toHtml();

        parent::__construct('', implode(PHP_EOL, $html));
    }

    /**
     * Renders the panel with the not authenticated message and the login form
     *
     * @return string
     */
    public function renderPanel()
    {
        $html = array();

        $html[] = '<div class="panel panel-danger panel-not-authenticated">';
        $html[] = '<div class="panel-heading">';
        $html[] = Translation::getInstance()->getTranslation('NotAuthenticated', array(), Utilities::COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $this->displayLoginForm();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
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

        $form = new FormValidator('formLogin', 'post', $redirect->getCurrentUrl());

        $form->get_renderer()->setElementTemplate('{element}');

        $form->setRequiredNote(null);

        $form->addElement('html', '<div class="form-group">');
        $form->addElement('html', '<div class="input-group">');

        $form->addElement(
            'html', '<div class="input-group-addon">' . $translator->getTranslation('Username') . '</div>'
        );

        $form->addElement(
            'text', 'login', Translation::get('UserName'),
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
            'password', 'password', Translation::get('Pass'),
            array('size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control')
        );

        $form->addElement('html', '</div>');
        $form->addElement('html', '</div>');

        $form->addElement('html', '<div class="form-group text-right">');
        $form->addElement(
            'style_submit_button', 'submitAuth', Translation::get('Login'), null, null, new FontAwesomeGlyph('log-in')
        );
        $form->addElement('html', '</div>');

        $form->addRule('password', Translation::get('ThisFieldIsRequired'), 'required');

        $form->addRule('login', Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required');

        return $form->toHtml();
    }
}