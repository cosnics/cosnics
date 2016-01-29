<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonSearchForm extends FormValidator
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
     * Creates a new search form
     *
     * @param string $url The location to which the search request should be posted.
     */
    public function __construct($url)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $url);
        $this->setAttribute('class', 'form-inline');
        $this->renderer = clone $this->defaultRenderer();

        $query = $this->getQuery();
        if ($query)
        {
            $this->setDefaults(array(self :: PARAM_SIMPLE_SEARCH_QUERY => $query));
        }

        $this->buildForm();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function buildForm()
    {
        $this->addElement(
            'text',
            self :: PARAM_SIMPLE_SEARCH_QUERY,
            Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES),
            'class="form-control input-sm"');

        $this->renderer->setElementTemplate(
            '<div class="form-group">
                <label class="sr-only">{label}</label>
                    {element}
            </div> ',
            self :: PARAM_SIMPLE_SEARCH_QUERY);

        $this->addElement(
            'style_submit_button',
            'submit',
            '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>',
            array('class' => 'btn btn-default btn-sm'));

        $buttonElementTemplate = '<div class="form-group"><label class="sr-only">{label}</label>{element}</div> ';

        $this->renderer->setElementTemplate($buttonElementTemplate, 'submit');

        if ($this->getQuery())
        {
            $this->addElement(
                'style_submit_button',
                'clear',
                '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>',
                array('class' => 'btn btn-default btn-sm', 'value' => 'clear'));
            $this->renderer->setElementTemplate($buttonElementTemplate, 'clear');
        }
    }

    /**
     * Display the form
     */
    public function render()
    {
        return $this->renderer->toHTML();
    }

    /**
     * Gets the conditions that this form introduces.
     *
     * @return String the query
     */
    public function getQuery()
    {
        $query = Request :: post(self :: PARAM_SIMPLE_SEARCH_QUERY);

        if (! $query)
        {
            $query = Request :: get(self :: PARAM_SIMPLE_SEARCH_QUERY);
        }

        return $query;
    }

    public function clearFormSubmitted()
    {
        return ! is_null(Request :: post('clear'));
    }
}
