<?php
namespace Chamilo\Core\Admin\UserInterface\Form;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_stylesubmitbutton;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm_html;
use HTML_QuickForm_text;

/**
 * @package Chamilo\Core\Admin\UserInterface\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 */
class AdminSearchForm extends FormValidator
{
    public const FORM_NAME = 'search';

    public const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    /**
     * @param string $url
     * @param string $form_id
     *
     * @throws \QuickformException
     */
    public function __construct(string $url, string $form_id = '')
    {
        parent::__construct(self::FORM_NAME . $form_id, self::FORM_METHOD_POST, $url, '', [], false);
        $this->updateAttributes(['id' => self::FORM_NAME . $form_id]);
        $this->build();
    }

    /**
     * @throws \QuickformException
     */
    private function build(): void
    {
        $renderer = $this->getRenderer();

        $renderer->setFormTemplate(
            '<form {attributes}>{content}</form>'
        );
        $renderer->setElementTemplate('{element}');

        $this->addElement(HTML_QuickForm_html::class, '<div class="input-group">');

        $this->addElement(
            HTML_QuickForm_text::class, self::PARAM_SIMPLE_SEARCH_QUERY, null, 'size="20" class="form-control"'
        );

        $this->addElement(HTML_QuickForm_html::class, '<span class="input-group-btn">');

        $this->addElement(
            HTML_QuickForm_stylesubmitbutton::class, 'submit',
            $this->getTranslator()->trans('Search', [], Manager::CONTEXT), null, null, new FontAwesomeGlyph('search')
        );

        $this->addElement(HTML_QuickForm_html::class, '</span>');
        $this->addElement(HTML_QuickForm_html::class, '</div>');
    }
}
