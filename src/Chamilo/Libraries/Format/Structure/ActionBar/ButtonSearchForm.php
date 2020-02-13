<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupportedSearchFormInterface;
use Chamilo\Libraries\Format\Table\Table;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
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
     *
     * @var \HTML_QuickForm_Renderer_Default
     */
    private $renderer;

    /**
     *
     * @var string
     */
    protected $actionURL;

    /**
     * Creates a new search form
     *
     * @param string $url The location to which the search request should be posted.
     */
    public function __construct($url)
    {
        parent::__construct(self::FORM_NAME, 'post', $url);

        $this->actionURL = $url;

        $this->setAttribute('class', 'form-inline');
        $this->renderer = clone $this->defaultRenderer();

        $query = $this->getQuery();
        if ($query)
        {
            $this->setDefaults(array(self::PARAM_SIMPLE_SEARCH_QUERY => $query));
        }

        $this->buildForm();
    }

    /**
     * Build the simple search form.
     */
    private function buildForm()
    {
        $this->renderer->setFormTemplate('<form {attributes}>{content}</form>');

        $this->addElement('html', '<div class="action-bar input-group pull-right">');

        $this->addElement(
            'text', self::PARAM_SIMPLE_SEARCH_QUERY, Translation::get('Search', null, Utilities::COMMON_LIBRARIES),
            array('class' => 'form-group form-control action-bar-search')
        );

        $this->renderer->setElementTemplate('{element} ', self::PARAM_SIMPLE_SEARCH_QUERY);

        $this->addElement('html', '<div class="input-group-btn">');

        $this->addElement('style_button', 'submit', null, null, 'submit', new FontAwesomeGlyph('search'));

        $buttonElementTemplate = '{element}';

        $this->renderer->setElementTemplate($buttonElementTemplate, 'submit');

        if ($this->getQuery())
        {
            $this->addElement('style_button', 'clear', null, null, 'clear', new FontAwesomeGlyph('remove'));
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
        $this->accept($this->renderer);

        return $this->renderer->toHTML();
    }

    /**
     * Gets the conditions that this form introduces.
     *
     * @return string
     */
    public function getQuery()
    {
        $query = Request::post(self::PARAM_SIMPLE_SEARCH_QUERY);

        if (!$query)
        {
            $query = Request::get(self::PARAM_SIMPLE_SEARCH_QUERY);
        }

        return $query;
    }

    /**
     *
     * @return boolean
     */
    public function clearFormSubmitted()
    {
        return !is_null(Request::post('clear'));
    }

    /**
     * Registers the table parameters in the form
     *
     * @param string[] $tableParameters
     */
    public function registerTableParametersInSearchForm(array $tableParameters = array())
    {
        foreach ($tableParameters as $tableParameter => $value)
        {
            $this->actionURL .= '&' . $tableParameter . '=' . $value;
        }

        $this->updateAttributes(array('action' => $this->actionURL));
    }

    /**
     * Registers the form parameters in the table
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     */
    public function registerSearchFormParametersInTable(Table $table)
    {
        $table->addParameter(self::PARAM_SIMPLE_SEARCH_QUERY, $this->getQuery());
    }

    /**
     * Returns the action URL
     *
     * @return string
     */
    public function getActionURL()
    {
        return $this->actionURL;
    }
}
