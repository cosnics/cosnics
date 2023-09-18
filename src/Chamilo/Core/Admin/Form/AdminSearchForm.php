<?php
namespace Chamilo\Core\Admin\Form;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\Admin\Form
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
        parent::__construct(self::FORM_NAME . $form_id, self::FORM_METHOD_POST, $url);
        $this->updateAttributes(['id' => self::FORM_NAME . $form_id]);
        $this->build();
    }

    /**
     * @throws \QuickformException
     */
    private function build()
    {
        $renderer = $this->get_renderer();

        $renderer->setFormTemplate(
            '<form {attributes}>{content}</form>'
        );
        $renderer->setElementTemplate('{element}');

        $this->addElement('html', '<div class="input-group">');

        $this->addElement(
            'text', self::PARAM_SIMPLE_SEARCH_QUERY, null, 'size="20" class="form-control"'
        );

        $this->addElement('html', '<span class="input-group-btn">');

        $this->addElement(
            'style_submit_button', 'submit', $this->getTranslator()->trans('Search', [], Manager::CONTEXT), null, null,
            new FontAwesomeGlyph('search')
        );

        $this->addElement('html', '</span>');
        $this->addElement('html', '</div>');
    }
}
