<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Theme;

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
        $this->setAttribute('class', 'form-inline');
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
        $this->addElement(
            'text',
            self :: PARAM_SIMPLE_SEARCH_QUERY,
            Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES),
            'class="form-control input-sm"');

        $this->renderer->setElementTemplate(
            '<div class="form-group">
                <label class="sr-only">{label}</label>
                <div class="input-group">
                    <div class="input-group-addon">' .
                        Theme :: getInstance()->getCommonImage('Action/Search') .
                    '</div>
                    {element}
                </div>
            </div>',
            self :: PARAM_SIMPLE_SEARCH_QUERY);

        $this->addElement(
            'style_submit_button',
            'submit',
            // Theme :: getInstance()->getCommonImage('Action/Search'),
            Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'btn btn-default btn-sm'));

        $this->renderer->setElementTemplate('{element}', 'submit');

        if ($this->get_query())
        {
            $this->addElement('style_submit_button', 'clear', null, array('class' => 'clear', 'value' => 'clear'));
            $this->renderer->setElementTemplate('{element}', 'clear');
        }
    }

    /**
     * Display the form
     */
    public function as_html()
    {
        return $this->renderer->toHTML();
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
