<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package common.html.action_bar $Id: action_bar_search_form.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 */
class ActionBarSearchForm extends FormValidator
{
    /**
     * #@+ Search parameter
     */
    const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    /**
     * Name of the search form
     */
    const FORM_NAME = 'search';

    /**
     * The renderer used to display the form
     */
    private $renderer;

    /**
     * Advanced or simple search form
     */
    private $advanced;

    /**
     * Creates a new search form
     *
     * @param string $url The location to which the search request should be posted.
     */
    public function __construct($url)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $url);
        $this->renderer = clone $this->defaultRenderer();

        $query = $this->get_query();
        if ($query)
        {
            $this->setDefaults(array(self :: PARAM_SIMPLE_SEARCH_QUERY => $query));
        }

        $this->build_simple_search_form();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_simple_search_form()
    {
        $this->renderer->setElementTemplate('<div style="vertical-align: middle; float: left;">{element}</div>');
        $this->addElement(
            'text',
            self :: PARAM_SIMPLE_SEARCH_QUERY,
            Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES),
            'size="20" class="search_query"');

        $this->addElement('style_submit_button', 'submit', null, array('class' => 'search'));

        if ($this->get_query())
        {
            $this->addElement('style_submit_button', 'clear', null, array('class' => 'clear', 'value' => 'clear'));
        }
    }

    /**
     * Display the form
     */
    public function as_html()
    {
        $html = array();
        $html[] = '<div class="simple_search">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    /**
     * Gets the conditions that this form introduces.
     *
     * @return String the query
     */
    public function get_query()
    {
        $query = Request :: post(self :: PARAM_SIMPLE_SEARCH_QUERY);
        if (! $query)
        {
            $query = Request :: get(self :: PARAM_SIMPLE_SEARCH_QUERY);
        }
        return $query;
    }
}
