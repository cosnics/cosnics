<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class DynamicSearchAction extends DynamicAction
{
    const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    public function __construct($namespace, $url)
    {
        $search_form = new FormValidator('search', 'post', $url);
        $search_form->addElement('text', self :: PARAM_SIMPLE_SEARCH_QUERY, Translation :: get('Search'), 'size="20"');
        $search_form->addElement(
            'style_submit_button',
            'submit',
            Translation :: get('Search'),
            array('class' => 'normal search'));

        $renderer = $search_form->get_renderer();
        $renderer->setFormTemplate(
            '<form {attributes}><div class="dynamic_search_action">{content}</div><div class="clear">&nbsp;</div></form>');
        $renderer->setElementTemplate('{element}');

        parent :: __construct(
            null,
            $search_form->toHtml(),
            Theme :: getInstance()->getImagePath($namespace, 'Admin/Search'),
            null);
    }
}
