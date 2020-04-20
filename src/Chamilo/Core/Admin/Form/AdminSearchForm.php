<?php
namespace Chamilo\Core\Admin\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package admin
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class AdminSearchForm extends FormValidator
{
    /**
     * Name of the search form
     */
    const FORM_NAME = 'search';

    const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    /**
     * @param string $url
     * @param string $form_id
     */
    public function __construct($url, $form_id = '')
    {
        parent::__construct(self::FORM_NAME . $form_id, self::FORM_METHOD_POST, $url);
        $this->updateAttributes(array('id' => self::FORM_NAME . $form_id));
        $this->build();
    }

    /**
     * Build the simple search form.
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
            'style_submit_button', 'submit', Translation::get('Search'), null, null, new FontAwesomeGlyph('search')
        );

        $this->addElement('html', '</span>');
        $this->addElement('html', '</div>');
    }
}
