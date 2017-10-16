<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicSearchAction extends DynamicAction
{
    const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    /**
     *
     * @param string $namespace
     * @param string $url
     */
    public function __construct($namespace, $url)
    {
        $search_form = new FormValidator('search', 'post', $url);
        $search_form->addElement('text', self::PARAM_SIMPLE_SEARCH_QUERY, Translation::get('Search'), 'size="20"');
        $search_form->addElement('style_button', 'submit', Translation::get('Search'), null, null, 'search');

        $renderer = $search_form->get_renderer();
        $renderer->setFormTemplate(
            '<form {attributes}><div class="dynamic_search_action">{content}</div><div class="clear">&nbsp;</div></form>');
        $renderer->setElementTemplate('{element}');

        parent::__construct(
            null,
            $search_form->toHtml(),
            Theme::getInstance()->getImagePath($namespace, 'Admin/Search'),
            null);
    }
}
