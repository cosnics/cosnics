<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Search\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SearchForm extends FormValidator
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
     * Creates a new search form
     *
     * @param string $url The location to which the search request should be posted.
     */
    public function __construct($url)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $url);

        $query = $this->getQuery();

        if ($query)
        {
            $this->setDefaults(array(self :: PARAM_SIMPLE_SEARCH_QUERY => $query));
        }

        $this->buildForm();
    }

    /**
     * Build the simple search form.
     */
    private function buildForm()
    {
        $this->addElement(
            'text',
            self :: PARAM_SIMPLE_SEARCH_QUERY,
            Translation :: get('SearchFor'),
            array('class' => 'form-control'));

        $this->addElement(
            'style_button',
            'submit',
            Translation :: get('Search'),
            array('class' => 'btn-primary'),
            'submit',
            'search');

        $renderer = $this->get_renderer();
        $renderer->setElementTemplate(
            '<div class="form-group"><label>{label}</label>{element}</div>',
            self :: PARAM_SIMPLE_SEARCH_QUERY);
        $renderer->setElementTemplate('{element}', 'submit');
    }

    /**
     * Display the form
     */
    public function render()
    {
        return $this->toHtml();
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
