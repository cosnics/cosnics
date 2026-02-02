<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Form;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_stylebutton;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm_html;
use HTML_QuickForm_Renderer_Default;
use HTML_QuickForm_text;
use QuickformException;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonSearchForm extends FormValidator
{
    public const FORM_NAME = 'search';
    public const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    protected string $actionUrl;

    private HTML_QuickForm_Renderer_Default $renderer;

    /**
     * @throws \QuickformException
     */
    public function __construct(string $url)
    {
        parent::__construct(self::FORM_NAME, self::FORM_METHOD_POST, $url, '', [], false);

        $this->actionUrl = $url;

        $this->setAttribute('class', 'form-inline');
        $this->renderer = clone $this->defaultRenderer();

        if ($this->getQuery()) {
            $this->setDefaults([self::PARAM_SIMPLE_SEARCH_QUERY => $this->getQuery()]);
        }

        $this->buildForm();
    }

    public function render(?string $in_data = null): string
    {
        $this->accept($this->renderer);

        return $this->renderer->toHtml();
    }

    /**
     * @throws \QuickformException
     */
    private function buildForm(): void
    {
        $this->renderer->setFormTemplate('<form {attributes}>{content}</form>');

        $this->addElement(HTML_QuickForm_html::class, '<div class="action-bar input-group pull-right">');

        $this->addElement(
            HTML_QuickForm_text::class, self::PARAM_SIMPLE_SEARCH_QUERY,
            $this->getTranslator()->trans('Search', [], StringUtilities::LIBRARIES),
            ['class' => 'form-group form-control action-bar-search']
        );

        $this->renderer->setElementTemplate('{element} ', self::PARAM_SIMPLE_SEARCH_QUERY);

        $this->addElement(HTML_QuickForm_html::class, '<div class="input-group-btn">');

        $this->addElement(
            HTML_QuickForm_stylebutton::class, 'submit', null, null, 'submit', new FontAwesomeGlyph('search')
        );

        $buttonElementTemplate = '{element}';

        $this->renderer->setElementTemplate($buttonElementTemplate, 'submit');

        if ($this->getQuery()) {
            $this->addElement(
                HTML_QuickForm_stylebutton::class, 'clear', null, null, 'clear', new FontAwesomeGlyph('times')
            );
            $this->renderer->setElementTemplate($buttonElementTemplate, 'clear');
        }

        $this->addElement(HTML_QuickForm_html::class, '</div>');
        $this->addElement(HTML_QuickForm_html::class, '</div>');
    }

    public function clearFormSubmitted(): bool
    {
        return !is_null($this->getRequest()->request->get('clear'));
    }

    public function getActionUrl(): string
    {
        return $this->actionUrl;
    }

    public function getQuery(): ?string
    {
        try {
            if (!$this->clearFormSubmitted()) {
                if ($this->validate()) {
                    return $this->getRequest()->request->get(self::PARAM_SIMPLE_SEARCH_QUERY);
                }
                else {
                    return $this->getRequest()->query->get(self::PARAM_SIMPLE_SEARCH_QUERY);
                }
            }
        }
        catch (QuickformException) {
            return null;
        }

        return null;
    }
}
