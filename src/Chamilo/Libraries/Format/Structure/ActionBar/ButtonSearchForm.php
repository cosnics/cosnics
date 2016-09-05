<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupportedSearchFormInterface;
use Chamilo\Libraries\Format\Table\Table;
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
class ButtonSearchForm extends FormValidator implements TableSupportedSearchFormInterface
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
        $this->renderer->setFormTemplate('<form {attributes}>{content}</form>');

        $this->addElement('html', '<div class="action-bar input-group pull-right">');

        $this->addElement(
            'text',
            self :: PARAM_SIMPLE_SEARCH_QUERY,
            Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'form-group form-control btn action-bar-search'));

        $this->renderer->setElementTemplate('{element} ', self :: PARAM_SIMPLE_SEARCH_QUERY);

        $this->addElement('html', '<div class="input-group-btn">');

        $this->addElement(
            'style_button',
            'submit',
            null,
            null,
            'submit',
            'search');

        $buttonElementTemplate = '{element}';

        $this->renderer->setElementTemplate($buttonElementTemplate, 'submit');

        if ($this->getQuery())
        {
            $this->addElement(
                'style_button',
                'clear',
                null,
                null,
                'clear',
                'remove');
            $this->renderer->setElementTemplate($buttonElementTemplate, 'clear');
        }

        $this->addElement('html', '</div>');
        $this->addElement('html', '</div>');
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

    /**
     * Registers the table parameters in the form
     *
     * @param array $tableParameters
     */
    public function registerTableParametersInForm(array $tableParameters = array())
    {
        // TODO: Implement registerTableParametersInForm() method.
    }

    /**
     * Registers the form parameters in the table
     *
     * @param Table $table
     */
    public function registerFormParametersInTable(Table $table)
    {
        $table->addParameter(self::PARAM_SIMPLE_SEARCH_QUERY, $this->getQuery());
    }
}
