<?php
namespace Chamilo\Libraries\Format\Response;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NotAuthenticatedResponse extends Response
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->initializeContainer();

        $html = [];

        $html[] = $this->getHeaderRenderer()->render();
        $html[] = $this->renderPanel();
        $html[] = $this->getFooterRenderer()->render();

        parent::__construct(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Exception
     */
    public function displayLoginForm(): string
    {
        $translator = $this->getTranslator();

        $form = new FormValidator('formLogin', FormValidator::FORM_METHOD_POST, $this->getRequest()->getUri());

        $form->get_renderer()->setElementTemplate('{element}');
        $form->get_renderer()->setRequiredNoteTemplate(null);

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

        return $form->render();
    }

    /**
     * @throws \Exception
     */
    public function renderPanel(): string
    {
        $html = [];

        $html[] = '<div class="row">';

        $html[] = '<div class="col-xs-12 col-md-2 col-lg-3"></div>';

        $html[] = '<div class="col-xs-12 col-md-8 col-lg-6">';
        $html[] = '<div class="panel panel-danger">';
        $html[] = '<div class="panel-heading">';
        $html[] = $this->getTranslator()->trans('NotAuthenticated', [], StringUtilities::LIBRARIES);
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