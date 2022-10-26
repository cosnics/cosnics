<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_QuickForm_Renderer_Default;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
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
     * @throws \Exception
     */
    public function __construct(string $url)
    {
        parent::__construct(self::FORM_NAME, self::FORM_METHOD_POST, $url);

        $this->actionUrl = $url;

        $this->setAttribute('class', 'form-inline');
        $this->renderer = clone $this->defaultRenderer();

        $query = $this->getQuery();

        if ($query)
        {
            $this->setDefaults([self::PARAM_SIMPLE_SEARCH_QUERY => $query]);
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
    private function buildForm()
    {
        $this->renderer->setFormTemplate('<form {attributes}>{content}</form>');

        $this->addElement('html', '<div class="action-bar input-group pull-right">');

        $this->addElement(
            'text', self::PARAM_SIMPLE_SEARCH_QUERY,
            $this->getTranslator()->trans('Search', [], StringUtilities::LIBRARIES),
            ['class' => 'form-group form-control action-bar-search']
        );

        $this->renderer->setElementTemplate('{element} ', self::PARAM_SIMPLE_SEARCH_QUERY);

        $this->addElement('html', '<div class="input-group-btn">');

        $this->addElement('style_button', 'submit', null, null, 'submit', new FontAwesomeGlyph('search'));

        $buttonElementTemplate = '{element}';

        $this->renderer->setElementTemplate($buttonElementTemplate, 'submit');

        if ($this->getQuery())
        {
            $this->addElement('style_button', 'clear', null, null, 'clear', new FontAwesomeGlyph('times'));
            $this->renderer->setElementTemplate($buttonElementTemplate, 'clear');
        }

        $this->addElement('html', '</div>');
        $this->addElement('html', '</div>');
    }

    public function clearFormSubmitted(): bool
    {
        return !is_null(Request::post('clear'));
    }

    public function getActionUrl(): string
    {
        return $this->actionUrl;
    }

    public function getQuery(): ?string
    {
        $query = $this->getRequest()->request->get(self::PARAM_SIMPLE_SEARCH_QUERY);

        if (!$query)
        {
            $query = $this->getRequest()->query->get(self::PARAM_SIMPLE_SEARCH_QUERY);
        }

        return $query;
    }

    /**
     * @deprecated Use ButtonSearchForm::getQuery() now
     */
    public function get_query(): string
    {
        return $this->getQuery();
    }
}
