<?php
namespace Chamilo\Libraries\Protocol\Authentication\Architecture\Response;

use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_button_submit;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseHeaderRenderer;
use HTML_QuickForm_html;
use HTML_QuickForm_password;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_text;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Architecture\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NotAuthenticatedResponse extends Response
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
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

        $form->getRenderer()->setElementTemplate('{element}');
        $form->getRenderer()->setRequiredNoteTemplate('');

        $form->addElement(HTML_QuickForm_html::class, '<div class="form-group">');
        $form->addElement(HTML_QuickForm_html::class, '<div class="input-group">');

        $form->addElement(
            HTML_QuickForm_html::class,
            '<div class="input-group-addon">' . $translator->trans('Username', [], StringUtilities::LIBRARIES) .
            '</div>'
        );

        $form->addElement(
            HTML_QuickForm_text::class, 'login', $translator->trans('Username', [], StringUtilities::LIBRARIES),
            ['size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control']
        );

        $form->addElement(HTML_QuickForm_html::class, '</div>');
        $form->addElement(HTML_QuickForm_html::class, '</div>');

        $form->addElement(HTML_QuickForm_html::class, '<div class="form-group">');
        $form->addElement(HTML_QuickForm_html::class, '<div class="input-group">');

        $form->addElement(
            HTML_QuickForm_html::class,
            '<div class="input-group-addon">' . $translator->trans('Password', [], StringUtilities::LIBRARIES) .
            '</div>'
        );

        $form->addElement(
            HTML_QuickForm_password::class, 'password', $translator->trans('Pass', [], StringUtilities::LIBRARIES),
            ['size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control']
        );

        $form->addElement(HTML_QuickForm_html::class, '</div>');
        $form->addElement(HTML_QuickForm_html::class, '</div>');

        $form->addElement(HTML_QuickForm_html::class, '<div class="form-group text-right">');
        $form->addElement(
            HTML_QuickForm_button_submit::class, 'submitAuth',
            $translator->trans('Login', [], StringUtilities::LIBRARIES), null, null, new FontAwesomeGlyph('sign-in-alt')
        );
        $form->addElement(HTML_QuickForm_html::class, '</div>');

        $form->addRule(
            'password', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class
        );
        $form->addRule(
            'login', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class
        );

        return $form->render();
    }

    protected function getFooterRenderer(): BaseFooterRenderer
    {
        return $this->getService(BaseFooterRenderer::class);
    }

    protected function getHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->getService(BaseHeaderRenderer::class);
    }

    /**
     * @throws \Exception
     */
    public function renderPanel(): string
    {
        $html = [];

        $html[] = '<div class="row">';

        $html[] = '<div class="col-12 col-md-2 col-lg-3"></div>';

        $html[] = '<div class="col-12 col-md-8 col-lg-6">';
        $html[] = '<div class="panel panel-danger">';
        $html[] = '<div class="panel-heading">';
        $html[] = $this->getTranslator()->trans('NotAuthenticated', [], StringUtilities::LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $this->displayLoginForm();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-12 col-md-2 col-lg-3"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}